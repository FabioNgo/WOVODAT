define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      // Serie = require('models/serie'),
      // TimeSerieGraph = require('views/time_serie_graph'),
      OverviewGraph = require('views/overview_graph'),
      TimeRange = require('models/time_range'),
      Eruption = require('models/eruption'),
      Eruptions = require('collections/eruptions'),
      EruptionSelect = require('views/eruption_select');


  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      /** Variable declaration **/
      this.timeRange = options.timeRange;
      this.overviewSelectingTimeRange = new TimeRange();
      this.observer = options.observer;
      
      this.overviewGraph = new OverviewGraph({
        collection: this.collection,
        timeRange: this.timeRange,
        selectingTimeRange: this.overviewSelectingTimeRange
      });
    },

    timeSeriesChanged: function(selectingTimeSeries) {
      this.selectingTimeSeries = selectingTimeSeries;
      if (this.selectingTimeSeries.length == 0) {
        this.overviewGraph.hide();
      }else{
        this.overviewGraph.show();
      }
      this.render();
    },

    render: function() {
      this.overviewGraph.$el.appendTo(this.$el);
    }
  });
});