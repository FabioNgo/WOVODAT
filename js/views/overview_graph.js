define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection','excanvas']),
      TimeRange = require('models/time_range'),
      MathHelper = require('helper/math');
  return Backbone.View.extend({
    initialize: function(options) {
      // _(this).bindAll(
      //   'update',
      //   'onSelect',
      //   'onTimeRangeChange',
      //   'onSelectingTimeRangeChange'
      //   );
      
      
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      // this.computeTimeRange();
      this.timeRange = new TimeRange();
      this.selectingTimeRange = options.selectingTimeRange;
      // this.listenTo(this.selectingTimeSeries, 'change remove', this.update);
      // this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      // this.listenTo(this.selectingTimeRange, 'change', this.onSelectingTimeRangeChange);
    },
    
    selectingFiltersChanged: function(selectingFilters) {
      this.selectingFilters = selectingFilters;
      // this.computeTimeRange();
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
      // console.log(this.selectingTimeSeries);
      this.$el.html("<div>Overview Graph <br></br></div>")
      
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
            // console.log(d);
            var time = d.time;
            var value = d.value;
          // d.stime_formated = DateHelper.formatDate(d.stime);
          // d.etime_formated = DateHelper.formatDate(d.etime);
          // d.time_formated = DateHelper.formatDate(d.time);
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
          data.push(this.formatGraphAppearance(list,filters[i].timeSerie.getName(),filters[i].name[j]));
          
        }

          
      }
      
      this.minX = minX;
      this.maxX = maxX;
      if(this.minX == this.maxX){
        this.minX = this.minX - 86400000;
        this.maxX = this.minX + 86400000;
      }
      
      /** setup y-axis tick **/
      if(maxY != undefined && minY != undefined){
        
        this.ticks = function(){
          var ticks = [];
          /** compute exponential Degree **/
          var expDeg = undefined
          if(MathHelper.exponentialDegree(minY) < MathHelper.exponentialDegree(maxY)){
            expDeg = MathHelper.exponentialDegree(maxY);
          }else{
            expDeg = MathHelper.exponentialDegree(minY)
          }
          var step = MathHelper.makeNumber((maxY-minY)/8,expDeg); // step of ticks
          /**** compute ticks ****/
          var startTick = MathHelper.makeNumber(minY -step,expDeg); // start tick
          var endTick = MathHelper.makeNumber(maxY+step,expDeg); // end tick
          var curTick = startTick;
          for(var i=0; curTick<endTick;i++){
            curTick = MathHelper.makeNumber(startTick + i *step,expDeg);
            ticks.push(curTick);
            
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