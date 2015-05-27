define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']);
  var template = require("text!templates/overview_graph.html");
      
  return Backbone.View.extend({
    initialize: function(options) {
      _(this).bindAll('update', 'onSelect', 'onTimeRangeChange', 'onSelectingTimeRangeChange');
      this.timeRange = options.timeRange;
      this.selectingTimeRange = options.selectingTimeRange;
      this.filterObserver = options.filterObserver;
      this.listenTo(this.collection, 'remove', this.update);
      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      this.listenTo(this.selectingTimeRange, 'change', this.onSelectingTimeRangeChange);
      this.listenTo(this.filterObserver, 'filter-change', this.update);
    },

    template : _.template(template),

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

      this.$el.html(this.template());

      this.graphContainer = this.$el.find("div").first();

      this.graphContainer.width(800);
      this.graphContainer.height(60);

      this.graph = $.plot(this.graphContainer, this.data, options);
      this.graphContainer.bind('plotselected', this.onSelect);

      return this;
    },

    update: function() {
      this.prepareData();
      this.render();
    },

    prepareData: function() {
      var minX = undefined,
          maxX = undefined,
          data = [],
          i;
      //console.log(this.collection);
      this.collection.models.forEach(function(serie) {
        serie.get("selectingFilter").models.forEach(function(filter) {
          var list = [];
          serie.get('data').forEach(function(d) {
            if ( !filter.get("filter") || _.isEqual(d.filter, filter.get("filter") ) ) {
              var x = d.start_time || d.time;
              if (minX === undefined || x < minX)
                minX = x;
              if (maxX === undefined || x > maxX)
                maxX = x;

              list.push([x, d.value]);
            }
          });
          data.push({
            data: list
          });
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