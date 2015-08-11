define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/series_tooltip'),
      TimeRange = require('models/time_range'),
      GraphHelper = require('helper/graph'),
      DateHelper = require('helper/date');

  return Backbone.View.extend({    
    initialize: function(options) {
      this.filters = options.filters;
      this.timeRange = new TimeRange();
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      this.prepareData();
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
    show: function(){
      
      // this.timeRangeChanged(this.timeRange);
      this.render();
    },
    render: function() {
      if(this.data==undefined){
        return;
      }
      this.$el.html("");
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
              ticks: this.ticks,
              labelWidth: 30,
              zoomRange: false,
            },
            grid: {
              hoverable: true,
            },
            zoom: {
              
              interactive: true,
              
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
      this.graph = $.plot(this.$el, this.data, options);
      this.$el.bind('plothover', this.tooltip,this.onHover);
      var eventData = {
        startTime: this.minX,
        endTime: this.maxX,
        data: this.data,
        graph: this.graph,
        el: this.$el,
        original_option: options
      }
      this.$el.bind('plotzoom',eventData, this.onZoom);
    },
    onZoom: function(event,plot){
      var option = event.data.original_option;
      var xaxis = plot.getXAxes()[0];
      var data = event.data.data;
      /* The zooming range cannot wider than the original range */
      if(xaxis.min<event.data.startTime || xaxis.max > event.data.endTime){
        option.xaxis.min = event.data.startTime;
        option.xaxis.max = event.data.endTime;
        event.data.graph = $.plot(event.data.el,data,option);
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
          var time = d.time;
          var value = d.value;
          d.time_formated = DateHelper.formatDate(d.time);
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
        data.push(GraphHelper.formatGraphAppearance(list,this.filters.timeSerie.getName(),this.filters.name[j]));
      }
      this.minX = minX;
      this.maxX = maxX;
      if(this.minX == this.maxX){
        this.minX = this.minX - 86400000;
        this.maxX = this.minX + 86400000;
      }
       if(minY!= undefined){
        this.minY = minY.toFixed();
      }else{
        this.minY = minY;
      }
      if(maxY != undefined && minY != undefined){
        this.ticks = GraphHelper.generateTick(minY,maxY);
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