define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection', 'jquery.flot.errorbars', 'jquery.flot.axislabels']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/series_tooltip'),
      TimeRange = require('models/time_range'),
      GraphHelper = require('helper/graph'),
      DateHelper = require('helper/date');

  return Backbone.View.extend({    
    initialize: function(options) {
      this.filters = options.filters;
      this.eruptionTimeRange = options.eruptionTimeRange;
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
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
      var unit = undefined;
      for(var i=0;i<this.data.length;i++){
        if(this.data[i].yaxis.axisLabel != undefined){
          unit = this.data[i].yaxis.axisLabel;
        }
      };
      var options = {
            // series: {
            //   points:{
            //     show: true,
            //     radius: 5,
            //     lineWidth: 2, // in pixels
            //     fill: true,
            //     fillColor: null,
            //     symbol: "circle" 
            //   },
            //   lines:{
            //     show: false
            //   },

            // },
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b-%Y",
              autoscale: true,
              min: this.minX,
              max: this.maxX
            },
            yaxis: {
              show: true,
              min: this.minY,
              max: this.maxY,
              ticks: this.ticks,
              labelWidth: 40,
              zoomRange: false,
              axisLabel: unit,
              axisLabelUseCanvas: true
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
      // plot the time series graph after being selected (eg. onSelect in OverViewGraph).
      // config graph theme colors
      options.colors = ["#000000", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"];
      this.graph = $.plot(this.$el, this.data, options);
      this.$el.bind('plothover', this.tooltip,this.onHover);
      var eventData = {
        startTime: this.minX,
        endTime: this.maxX,
        data: this.data,
        graph: this.graph,
        el: this.$el,
        self: this,
        original_option: options
      }
      this.$el.bind('plotzoom',eventData, this.onZoom);
    },
    onZoom: function(event,plot){
      var option = event.data.original_option;
      var xaxis = plot.getXAxes()[0];
      var data = event.data.data;
      var self = event.data.self;
      /* The zooming range cannot wider than the original range */
      if(xaxis.min<event.data.startTime || xaxis.max > event.data.endTime){
        option.xaxis.min = event.data.startTime;
        option.xaxis.max = event.data.endTime;

        event.data.graph = $.plot(event.data.el,data,option);
        self.setUpTimeranges(option.xaxis.min,option.xaxis.max);
      }else{
        self.setUpTimeranges(xaxis.min,xaxis.max);
      }

    },
    setUpTimeranges: function(startTime, endTime){
      // this.serieGraphTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // // console.log(this.serieGraphTimeRange);
      
      // this.serieGraphTimeRange.trigger('update',this.serieGraphTimeRange);
      // this.forecastsGraphTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // this.forecastsGraphTimeRange.trigger('update',this.forecastsGraphTimeRange);
      // this.eruptionTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // this.eruptionTimeRange.trigger('update',this.eruptionTimeRange);


    },
    prepareData: function() {
      if(this.filters == undefined){
        this.data = undefined;
        return;
      }
      var filters = [this.filters];
      GraphHelper.formatData(this,filters,true,true); //formatData: function(graph,filters,allowErrorbar,allowAxisLabel)
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