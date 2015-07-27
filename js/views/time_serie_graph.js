define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/tooltip'),
      TimeRange = require('models/time_range'),
      DateHelper = require('helper/date');

  return Backbone.View.extend({    
    initialize: function(options) {
      // _(this).bindAll(
      //   // 'prepareDataAndRender',
      //   'onTimeRangeChange'
      //   // 'onHover',
      //   // 'onPan'
      // );
      this.filters = options.filters;

      // console.log(this.timeSerie);
      this.timeRange = new TimeRange();
      // this.timeRange = options.timeRange;
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      this.prepareData();
      // this.model.fetch();
      // this.show();
      
      // this.listenTo(this.model, 'change', this.prepareDataAndRender);
    },

    timeRangeChanged: function(TimeRange){
      if(TimeRange == undefined){
        return;
      }
      this.minX = TimeRange.get('startTime');
      this.maxX = TimeRange.get('endTime');
      // this.render();
    },

    onHover: function(event, pos, item) {
      var tooltip = event.data;
      tooltip.update(pos, item);
    },

    // onPan: function() {
    //   var startTime = this.graph.getAxes().xaxis.options.min,
    //       endTime = this.graph.getAxes().xaxis.options.max;
      
    //   this.stopListening(this.timeRange, 'change');
    //   this.timeRange.set({
    //     startTime: startTime,
    //     endTime: endTime
    //   });
    //   this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
    // },
    show: function(){
      
      // this.timeRangeChanged(this.timeRange);
      this.render();
    },
    render: function() {
      // this.data = this.timeSerie.get('data');
      if(this.data==undefined){
        return;
      }
      // console.log(this.timeSerie);
      this.$el.html("");
      // var date = new DateHelper();
      
      // console.log(data);
      
      

      
      // console.log(this.timeSerie);
      var options = {
            series: {
              lines: { 
                show: true
              },
            },
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b-%Y",
              autoscale: true,
              min: this.minX,
              max: this.maxX
            },
            yaxis: {
              show: true,
              tickFormatter: function(val, axis) { 
                // console.log(val);
                if(val > 9999 || val <-9999){
                  val = val.toPrecision(1);
                }else{
                  
                }
                return val
              },
              // max: this.maxY,
              // min: this.minY,
              ticks: this.ticks,
              labelWidth: 30
            },
            grid: {
              hoverable: true,
            },
            tooltip:{
              show: true,
            },
            
          };
          
      if (!this.data || !this.data.length) {
        this.$el.html('');
        return;
      }
      // console.log(this.data);
      this.$el.width('auto');
      this.$el.height(200);
      this.$el.addClass('time-serie-graph');
      // this.$el.bind('pageshow',function(){    
      //   $.plot(this.$el, this.data, options);
      // });
      this.graph = $.plot(this.$el, this.data, options);
      // console.log(this.graph);
      this.$el.bind('plothover', this.tooltip,this.onHover);
      var eventData = {
        startTime: this.startTime,
        endTime: this.endTime,
        param_ds: this.param_ds,
        graph: this.graph,
        el: this.$el
      }
      // this.$el.bind('plotzoom',eventData, this.onZoom);
    },
    onZoom: function(event,plot){
      var option = plot.getOptions();
      var xaxis = plot.getXAxes()[0];
      var data = event.data;
      /* The zooming range cannot wider than the original range */
      if(xaxis.min<data.startTime || xaxis.max > data.endTime){
        option.xaxis.min = data.startTime;
        option.xaxis.max = data.endTime; 
        event.data.graph = $.plot(event.data.el,[data.param_ds],option);
      }
      
      
      
      
    },
    // setup effect for the graph
    formatGraphAppearance: function(data,timeSerieName, filterName){
      
      return {
        data: data,
        label: filterName + ":"+timeSerieName,
        lines: { 
          show: true
        },
        shadowSize: 3,
        points: {
          show: true,
          radius: 1,
          symbol: "circle",
          // fillColor: "#EDC240"
        },
        // color: "#EDC240"
      }
    },
    prepareData: function() {
      if(this.filters == undefined){
        this.data = undefined;
        return;
      }
      var minX = undefined,
          maxX = undefined,
          minY = undefined,
          maxY = undefined,
          data = [],
          i;
      
      

      for(var j = 0; j<this.filters.name.length;j++){ 
        var list = [];
        var filterData = this.filters.timeSerie.getDataFromFilter(this.filters.name[j])
        filterData.forEach(function(d) {
          // console.log(d);
          // var start_time = d.time;
          // var end_time = d.time;
          var time = d.time;
          var value = d.value;
          // d.stime_formated = DateHelper.formatDate(d.stime);
          // d.etime_formated = DateHelper.formatDate(d.etime);
          d.time_formated = DateHelper.formatDate(d.time);
          // var x = d.start_time || d.time;
          if (minX === undefined || time < minX){
            minX = time;
          }
          if (maxX === undefined || time > maxX){
            maxX = time;
          }
          if (minY === undefined || value < minY){
            minY = value;
          }
          if (maxY === undefined || value > maxY){
            maxY = value;
          }

          list.push([d['time'],d['value']]);
        });
        data.push(this.formatGraphAppearance(list,this.filters.timeSerie.getName(),this.filters.name[j]));
      }

      
      

      this.minX = minX*0.9;
      this.maxX = maxX*1.1;
       if(minY!= undefined){
        this.minY = minY.toFixed();
      }else{
        this.minY = minY;
      }
      if(maxY != undefined && minY != undefined){
        this.ticks = function(){
          var ticks = [];
          
          var step = (maxY - minY) /8.0;
          var preTick = (minY -step*2).toPrecision(1); // previous tick
          for(var i = -1;i<=8;i++){
            var curTick = (minY + step*i).toPrecision(1); // current tick
            if(curTick != preTick){
              ticks.push(curTick);
              preTick = curTick;
            }
          }
          return ticks;
        };
      }
      this.timeRange.set({
        'startTime': this.minX,
        'endTime': this.maxX,
      });
      // this.timeRange.trigger('change');
      this.data = data;
    },

    destroy: function() {
      // From StackOverflow with love.
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});