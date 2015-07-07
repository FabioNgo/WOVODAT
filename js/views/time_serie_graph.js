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
      this.timeSerie = options.timeSerie;

      // console.log(this.timeSerie);
      this.timeRange = new TimeRange();
      // this.timeRange = options.timeRange;
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      // this.model.fetch();
      this.prepareDataAndRender();
      
      // this.listenTo(this.model, 'change', this.prepareDataAndRender);
    },

    timeRangeChanged: function(TimeRange){
      if(TimeRange == undefined){
        return;
      }
      this.minX = TimeRange.get('startTime');
      this.maxX = TimeRange.get('endTime');
      this.render();
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
            
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b-%Y",
              autoscale: true,
              min: this.minX,
              max: this.maxX
            },
            yaxis: {
              show: true,
            },
            grid: {
              hoverable: true,
            },
            tooltip:{
              show: true,
              content: "a",
            },
            
          };
          
      if (!this.data || !this.data.length) {
        this.$el.html('');
        return;
      }
      // console.log(this.data);
      this.$el.width(800);
      this.$el.height(100);
      
      this.graph = $.plot(this.$el, this.data, options);
      console.log(this.graph);
      this.$el.bind('plothover', this.tooltip,this.onHover);
      var eventData = {
        startTime: this.startTime,
        endTime: this.endTime,
        param_ds: this.param_ds,
        graph: this.graph,
        el: this.$el
      }
      this.$el.bind('plotzoom',eventData, this.onZoom);
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
    formatGraphAppearance: function(data){
      return {
        data: data,
        label: this.timeSerie.getName(),
        lines: { 
          show: true
        },
        shadowSize: 5,
        points: {
          show: true,
          size: 1,
          symbol: "circle",
          fillColor: "#EDC240"
        },
        color: "#EDC240"
      }
    },
    prepareDataAndRender: function() {
      var minX = undefined,
          maxX = undefined,
          data = [],
          i;

      
      var list = [];
      if (this.timeSerie.get('data')) {
        this.timeSerie.get('data').forEach(function(d) {
          // console.log(d);
          // var start_time = d.time;
          // var end_time = d.time;
          var time = d.time;
          // d.stime_formated = DateHelper.formatDate(d.stime);
          // d.etime_formated = DateHelper.formatDate(d.etime);
          d.time_formated = DateHelper.formatDate(d.time);
          // var x = d.start_time || d.time;
          if (minX === undefined || time < minX)
            minX = time;
          if (maxX === undefined || time > maxX)
            maxX = time;

          list.push([d['time'],d['value']]);
        });
      }

      data.push(this.formatGraphAppearance(list));
      

      this.minX = minX;
      this.maxX = maxX;
      this.timeRange.set({
        'startTime': this.minX,
        'endTime': this.maxX,
      });
      // this.timeRange.trigger('change');
      this.data = data;
      this.render();
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