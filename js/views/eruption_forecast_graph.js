define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Const = require('helper/const'),
      ed_phs_forTemplate = require('text!templates/tooltip_ed_phs_for.html'),
      Tooltip = require('views/tooltip');

  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      _(this).bindAll('render', 'onHover', 'onTimeRangeChange', 'onDataChange', 'onVolcanoChange');
      
      this.observer = options.observer;
      this.timeRange = options.timeRange;
      this.volcano = options.volcano;

      this.tooltip = new Tooltip({
        template: ed_phs_forTemplate
      });

      this.listenTo(this.volcano, 'change', this.onVolcanoChange);
      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      this.listenTo(this.collection, 'sync', this.onDataChange);
    },

    previousHover: {
      dataIndex: null,
      savedContent: null
    },
    
    onHover: function(event, pos, item) {
      this.tooltip.update(pos, item);
    },

    onDataChange: function() {
      // Prepares data.
      var data = this.collection.models,
          ed_forData = [];

      data.forEach(function(ed_for) {
        var ed_for_astime = ed_for.get('ed_for_astime'),
            ed_for_aetime = ed_for.get('ed_for_aetime');
        ed_forData.push([ed_for_astime, 2, 0, ed_for_aetime - ed_for_astime, ed_for.attributes]);
      });

      // Saves prepared data to the view object.
      this.data = ed_forData;

      this.render({
        startTime: this.startTime,
        endTime: this.endTime,
        data: this.data
      });
    },

    onVolcanoChange: function() {
      this.collection.changeVolcano(this.volcano.get('vd_id'));
    },

    onTimeRangeChange: function() {
      this.startTime = this.timeRange.get('startTime');
      this.endTime = this.timeRange.get('endTime');
      this.render({
        startTime: this.startTime,
        endTime: this.endTime,
        data: this.data
      });
    },

    render: function(options) {
      var el = this.$el,
          param_ed_for = {
            label: 'Alert level',
            data: options.data,
            bars: {
              show: true,
              wovodat: true,
              drawBottom: true,
              lineWidth: 0
            },
            dataType: 'ed_for'
          },
          option = {
            grid: {
              hoverable: true
            },
            xaxis: {
              min: options.startTime,
              max: options.endTime,
              autoscale: true,
              mode: 'time',
              timeformat: '%Y-%m'
            },
            yaxis: {
              show:false,
              panRange: false
            }
          };
          
      el.width(800);
      el.height(60);

      $.plot(el, [param_ed_for], option);
      el.bind('plothover', this.onHover);
    },
  });
});