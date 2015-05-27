define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/tooltip'),
      DateHelper = require('helper/date');

  return Backbone.View.extend({    
    initialize: function(options) {
      _(this).bindAll('prepareDataAndRender', 'onTimeRangeChange', 'onHover', 'onPan');

      this.timeRange = options.timeRange;
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      this.model.fetch();
      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      this.listenTo(this.model, 'change', this.prepareDataAndRender);
    },

    onTimeRangeChange: function() {
      this.render();
    },

    onHover: function(event, pos, item) {
      this.tooltip.update(pos, item);
    },

    onPan: function() {
      var startTime = this.graph.getAxes().xaxis.options.min,
          endTime = this.graph.getAxes().xaxis.options.max;
      
      this.stopListening(this.timeRange, 'change');
      this.timeRange.set({
        startTime: startTime,
        endTime: endTime
      });
      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
    },

    render: function() {
      var param_ds = {
            color: '#5EB7FF',
            label: 'Data Series',
            data: this.data,
            bars: {
              show: true,
              wovodat: true
            },
            dataType: 'ds'
          },
          option = {
            grid: {
              hoverable: true,
            },
            xaxis: {
              min: this.timeRange.get('startTime'),
              max: this.timeRange.get('endTime'),
              mode: 'time',
              autoscale: true
            },
            yaxis: {
              min: 0,
              max: 200,
              autoscale: true,
              panRange: false,
              zoomRange: false
            },
            pan: {
              interactive: true
            },
            zoom: {
              interactive: true
            }
          };

      this.$el.html('<div></div>');
      this.$el.width(800);
      this.$el.height(200);

      this.graph = $.plot(this.$el, [param_ds], option);
      this.$el.bind('plothover', this.onHover);
      this.$el.bind('plotpan', this.onPan);
      this.$el.bind('plotzoom', this.onPan);
    },

    prepareDataAndRender: function() {
      var i,
          data = this.model.get('data'),
          a = [];
      this.model.get('data').forEach(function(ds) {
        ds.formattedStartTime = DateHelper.formatDate(ds.start_time);
        ds.formattedEndTime = DateHelper.formatDate(ds.end_time);
        a.push([ds.start_time, ds.value, 0, ds.end_time - ds.start_time, ds]);
      });
      this.data = a;
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