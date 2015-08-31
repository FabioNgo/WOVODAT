define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection','excanvas']),
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
            series: {
              lines: { 
                show: true
              },
            },
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
              // show: true,
              color: '#00000000',
              tickFormatter: function(val, axis) { 
                // console.log(val);
                if(val > 9999 || val <-9999){
                  val = val.toPrecision(1);
                }else{
                  
                }
                return val;
              },
              // max: this.maxY,
              // min: this.minY,
              ticks: this.ticks,
              labelWidth: 30
            },
            selection: { 
              mode: 'x', 
              color: '#451A2B' 
            }
          };

      if (!this.data || !this.data.length) {
        this.$el.html('');
        return;
      }
      // console.log(this.data);
      this.$el.width('auto');
      this.$el.height(200);
      this.$el.addClass("overview-graph");

      // console.log(this.data);
      this.graph = $.plot(this.$el, this.data, options);
      this.$el.bind('plotselected', this.selectingTimeRange,this.onSelect);
    },

    update: function() {
      this.prepareData();
      this.render();
    },
    
    
    prepareData: function() {
      var minX = undefined,
          maxX = undefined,
          minY = undefined,
          maxY = undefined,
          data = [],
          i;
      var filters = this.selectingFilters.models;
      for(i=0;i<filters.length;i++){
        for(var j = 0; j<filters[i].name.length;j++){
          var list = [];
          var filterData = filters[i].timeSerie.getDataFromFilter(filters[i].name[j])
          filterData.forEach(function(d) {
            var time = d.time;
            var value = d.value;
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
          data.push(GraphHelper.formatGraphAppearance(list,filters[i].timeSerie.getName(),filters[i].name[j]));
          
        }

          
      }
      
      this.minX = minX;
      this.maxX = maxX;
      if(this.minX == this.maxX){
        this.minX = this.minX - 86400000;
        this.maxX = this.maxX + 86400000;
      }
      
      /** setup y-axis tick **/
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