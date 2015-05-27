define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection' , 'jquery.flot.axislabels']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/tooltip'),
      Filter = require('views/filter'),
      DateHelper = require('helper/date');

  var template = require("text!templates/time_serie_graph.html");

  return Backbone.View.extend({    
    template : _.template(template),
    initialize: function(options) {
      _(this).bindAll('prepareDataAndRender', 'onTimeRangeChange', 'onHover', 'onPan');

      this.timeRange = options.timeRange;
      this.filter = options.filter;

      //console.log(this.filter);
      
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate,
        model : this.model
      });

      this.listenTo(this.timeRange, 'change', this.onTimeRangeChange);
      this.listenTo(this.filter, "change" , this.prepareDataAndRender);

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
              show: this.bars,
              wovodat: true
            },
            lines: {
              show: this.lines,
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
              //axisLabel: "\u03BC Radians",
              //axisLabelUseCanvas: true,
              //axisLabelFontSizePixels : 10,
              min: this.minValue,
              max: this.maxValue,
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

      this.$el.html( this.template( {
        name : this.filter.get("filter")
      }) );

      this.graphHolder = this.$el.find("div").first();

      this.graphHolder.width(800);
      this.graphHolder.height(200);

      this.graph = $.plot(this.graphHolder, [param_ds], option);
      this.graphHolder.bind('plothover', this.onHover);
      this.graphHolder.bind('plotpan', this.onPan);
      this.graphHolder.bind('plotzoom', this.onPan);

      return this;
    },

    prepareDataAndRender: function() {
      var i,
          data = this.model.get('data'),
          a = [],
          category = this.model.get('category'),
          selectedFilter = this.filter.get('filter'),
          that = this;
      //console.log(selectedFilter);
      if (data[0] && data[0].stime)
        this.bars = true;
      if (data[0] && data[0].time)
        this.lines = true;
      this.maxValue = 0;
      this.minValue = 0;
      this.model.get('data').forEach(function(ds) {
        if ( (!selectedFilter)  || _.isEqual(ds.filter, selectedFilter)) {
          ds.formattedStartTime = DateHelper.formatDate(ds.stime);
          ds.formattedEndTime = DateHelper.formatDate(ds.etime);
          if (ds.stime)
            a.push([ds.stime, ds.value, 0, ds.etime - ds.stime, ds]);
          else 
            a.push([ds.time, ds.value, 0, ds]);
          that.maxValue = Math.max(that.maxValue, ds.value);
          that.minValue = Math.min(that.minValue, ds.value);
        }
      });
      this.data = a;
      //console.log(a.length);
      //console.log(a);
      this.render(); 
    },

    destroy: function() {
      // From StackOverflow with love.
      //console.log("destroy");
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});