define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']);

  return Backbone.View.extend({
    initialize: function(options) {
      _(this).bindAll('update', 'onSelect', 'onTimeRangeChange', 'onSelectingTimeRangeChange');
      this.timeRange = options.timeRange;
      this.selectingTimeRange = options.selectingTimeRange;
      this.listenTo(this.collection, 'change remove', this.update);
      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      this.listenTo(this.selectingTimeRange, 'change', this.onSelectingTimeRangeChange);
    },

    onSelect: function(event, ranges) {
      var startTime = ranges.xaxis.from,
          endTime = ranges.xaxis.to;
      this.stopListening(this.selectingTimeRange);
      this.selectingTimeRange.set({
        startTime: startTime,
        endTime: endTime
      });
      this.listenTo(this.selectingTimeRange, 'change', this.onSelectingTimeRangeChange);
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

    onTimeRangeChange: function() {
      this.render();
      this.selectingTimeRange.set({
        startTime: this.timeRange.get('startTime'),
        endTime: this.timeRange.get('endTime')
      });
    },

    render: function() {
      this.$el.html("Overview Graph <br></br>")
      var options = {
            series: {
              lines: { 
                show: true
              },
              shadowSize: 0
            },
            xaxis: { 
              mode:'time',
              autoscale: true,
              min: this.timeRange.get('startTime'),
              max: this.timeRange.get('endTime')
            },
            yaxis: {
              show: false
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
      
      this.$el.width(800);
      this.$el.height(60);
      
      this.graph = $.plot(this.$el, this.data, options);
      this.$el.bind('plotselected', this.onSelect);
    },

    update: function() {
      this.prepareData();
      this.render();
    },

    prepareData: function() {
      var minX = 0,
          maxX = 0,
          data = [],
          i;
      //console.log(this.collection);
      this.collection.models.forEach(function(serie) {
        serie.get("selectingFilter").models.forEach(function(filter) {
          // var list = [];
          // var lines = false;
          // var bars = false;
          // var points = false;

          // serie.get('data').forEach(function(d) {
          //   if ( !filter.get("filter") || _.isEqual(d.filter, filter.get("filter") ) ) {
          //     var x = d.stime || d.time;
          //     if (d.time) lines = true;
          //     if (d.stime) bars = true;
          //     if (minX === undefined || x < minX)
          //       minX = x;
          //     if (maxX === undefined || x > maxX)
          //       maxX = x;

          //     list.push([x, d.value, 0, ( d.stime || d.time ) - ( d.etime || d.time ) ]);
          //   }
          // });

          // if ( serie.get("data_type") == "Evn" ) {
          //   lines = false;
          //   bars = false;
          //   points = true;
          // }

          // data.push({
          //   data: list,
          //   bars: {
          //     show: bars,
          //     wovodat: true
          //   },
          //   lines: {
          //     show: lines,
          //     wovodat: true
          //   },
          //   points : {
          //     show: points,
          //     symbol: "circle",
          //     wovodat: true,
          //     radius: 1
          //   }
          // });

          var list = serie.prepareDataForGraph(filter);
          //console.log(list.data);
          data.push( {
            data : list.data,
            bars: {
              show : list.bars,
              wovodat : true
            },
            lines : {
              show : list.lines,
              wovodat : true
            },
            points : {
              show : list.points,
              symbol: "circle",
              radius: 1,
              wovodat : true
            }
          });

          minX = Math.min(minX, list.minValue);
          maxX = Math.max(maxX, list.maxValue);

        });
      
      });

      this.minX = minX;
      this.maxX = maxX;
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