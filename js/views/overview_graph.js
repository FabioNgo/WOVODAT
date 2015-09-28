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
              min: this.minY,
              ticks: this.ticks,
              labelWidth: 30
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



      //console.log(this.data);
      this.graph = $.plot(this.$el, this.data, options);
      //To edit the series object, go to GraphHelper used for data in the prepareData method below.
      this.$el.bind('plotselected', this.selectingTimeRange, this.onSelect);
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
          errorbars = undefined,
          i;
      var filters = this.selectingFilters.models;
      for(i=0;i<filters.length;i++){
        for(var j = 0; j<filters[i].name.length;j++){
          var list = [];
          var filterData = filters[i].timeSerie.getDataFromFilter(filters[i].name[j])
          filterData.forEach(function(d) {
            var time = d.time;
            var value = d.value;
            var error = parseFloat(d.error);
            if(error == undefined){
              error == 0;
            }
            if (minX === undefined || time < minX){
              minX = time;
            }
            if (maxX === undefined || time > maxX){
              maxX = time;
            }
            if (minY === undefined || value-error < minY){
              minY = value-error;
            }
            if (maxY === undefined || value+error > maxY){
              maxY = value+error;
            }
            /*
            * Data for error bar: d[x,y,left,right,up,down]
            **/
            if(d['error']!=undefined){
              list.push([d['time'],d['value'],d['error']]); 
              errorbars = "y";
            }else{
              list.push([d['time'],d['value']]);  
            }
            
          });
          
            data.push(GraphHelper.formatGraphAppearance(list,filters[i].timeSerie.getName(),filters[i].name[j],errorbars));
          
          
        }

          
      }
      
      this.minX = minX-86400000;
      this.maxX = maxX+86400000;
      
      
      /** setup y-axis tick **/
      if(maxY != undefined && minY != undefined){
        maxY = maxY*1.1;
        minY = minY*0.9;
        this.ticks = GraphHelper.generateTick(minY,maxY);
        this.minY = this.ticks[0];
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