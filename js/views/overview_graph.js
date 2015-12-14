define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection','excanvas','jquery.flot.errorbars']),
      TimeRange = require('models/time_range'),
      GraphHelper = require('helper/graph');
  return Backbone.View.extend({
    initialize: function(options) {
      
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.timeRange = options.overviewGraphTimeRange;
      this.selectingTimeRange = options.selectingTimeRange;
    },
    
    selectingFiltersChanged: function(selectingFilters) {
      this.selectingFilters = selectingFilters;
      if(selectingFilters.length == 0){
        this.hide();
      }
      this.update();
    },
    onSelect: function(event, ranges) {

      var startTime = ranges.xaxis.from,
          endTime = ranges.xaxis.to;
      event.data.set({
        'startTime': startTime,
        'endTime': endTime,
      });
      event.data.trigger('update');
    },
    hide: function(){
      this.$el.html("");
      this.$el.width(0);
      this.$el.height(0);
      this.trigger('hide');
    },
    render: function() {
      
      var options = {
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b-%Y",
              autoscale: true,
              axisLabelUseCanvas: true,
              rotateTicks: 90,
              min: this.minX,
              max: this.maxX,
              autoscaleMargin: 10,
            },
            yaxis: {
              show: true,
              color: '#00000000',
              // tickFormatter: function(val, axis) { 
              //   // console.log(val);
              //   if(val > 9999 || val <-9999){
              //     val = val.toPrecision(1);
              //   }else{
                  
              //   }
              //   return val;
              // },
              min: this.minY,
              max: this.maxY,
              //axisLabelUseCanvas: true,
              autoscaleMargin: 5,
              ticks: this.ticks,
              labelWidth: 40
            },
            selection: { 
              mode: 'x', 
              color: '#451A2B' 
            },
            zoom: {
              interactive: false,
            },
          };
          //pass color into options
          options.colors = ["#000000", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"];

      if (!this.data || !this.data.length) {
        this.$el.html(''); //$(this) = this.$el
        return;
      };

      this.$el.width('auto');
      this.$el.height(200);
      this.$el.addClass("overview-graph");

      //limit data to be rendered
      
      // console.log(this.data);
      this.graph = $.plot(this.$el, this.data, options);
      //To edit the series object, go to GraphHelper used for data in the prepareData method below.
      this.$el.bind('plotselected', this.selectingTimeRange, this.onSelect);
    },

    update: function() {
      this.prepareData();
      this.render();
    },
    
  
    prepareData: function() {
     
      var filters = this.selectingFilters.models;
      var allowErrorbar = false;
      var allowAxisLabel =false;
      var limitNumberOfData =true;
      //formatData: function(graph,filters,allowErrorbar,allowAxisLabel,limitNumberOfData)
      GraphHelper.formatData(this,filters,allowErrorbar,allowAxisLabel,limitNumberOfData); 
      
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