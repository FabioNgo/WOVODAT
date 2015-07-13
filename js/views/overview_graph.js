define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']),
      TimeRange = require('models/time_range');

  return Backbone.View.extend({
    initialize: function(options) {
      // _(this).bindAll(
      //   'update',
      //   'onSelect',
      //   'onTimeRangeChange',
      //   'onSelectingTimeRangeChange'
      //   );
      
      
      this.timeRange = options.timeRange;
      // this.computeTimeRange();
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
      // this.stopListening(this.selectingTimeRange);
      event.data.set({
        'startTime': ranges.xaxis.from,
        'endTime': ranges.xaxis.to,
      });
      // this.listenTo(this.selectingTimeRange, 'change', this.onSelectingTimeRangeChange);
      event.data.trigger('change');
    },

    onSelectingTimeRangeChange: function() {
      if (!this.graph)
        return;
      this.graph.setSelection({ 
        xaxis: { 
          from: Math.max(this.selectingTimeRange.get('startTime'), this.timeRange.get('startTime')), 
          to: Math.min(this.selectingTimeRange.get('endTime'), this.timeRange.get('endTime'))
        }
      });
    },

    // onTimeRangeChange: function() {
    //   this.render();
    //   this.selectingTimeRange.set({
    //     startTime: this.timeRange.get('startTime'),
    //     endTime: this.timeRange.get('endTime')
    //   });
    // },
    hide: function(){
      this.$el.html("");
      this.$el.width(0);
      this.$el.height(0);
      this.trigger('hide');
    },
    render: function() {
      // console.log(this.selectingTimeSeries);
      this.$el.html("<div>Overview Graph <br></br></div>")
      // for (var i = 0; i < this.selectingTimeSeries.length; i++) {
      //   this.selectingTimeSeries[i].fetch();
      // };
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
              max: this.maxX,
            },
            yaxis: {
              show: true
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
      this.$el.width(800);
      this.$el.height(100);
      
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
          // d.stime_formated = DateHelper.formatDate(d.stime);
          // d.etime_formated = DateHelper.formatDate(d.etime);
          // d.time_formated = DateHelper.formatDate(d.time);
          // var x = d.start_time || d.time;
          if (minX === undefined || time < minX)
            minX = time;
          if (maxX === undefined || time > maxX)
            maxX = time;

            list.push([d['time'],d['value']]);
          });
          data.push(this.formatGraphAppearance(list,filters[i].timeSerie.getName(),filters[i].name[j]));
          
        }

          
      }
      
      this.minX = minX;
      this.maxX = maxX;
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