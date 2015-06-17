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
      _(this).bindAll('fetchEruptions', 'timeSeriesChanged');
      /** Variable declaration **/
      var selectingEruption = new Eruption();
      var eruptions = new Eruptions();
      var selectingVolcano = this.selectingVolcano;

      this.timeRange = options.timeRange;
      this.overviewSelectingTimeRange = new TimeRange();
      this.observer = options.observer;
      this.volcano = options.volcano;
      
      this.overviewGraph = new OverviewGraph({
        collection: this.collection,
        timeRange: this.timeRange,
        selectingTimeRange: this.overviewSelectingTimeRange
      });

      this.eruptionSelect = new EruptionSelect({
        collection: new Eruptions(),
        observer: this.observer,
        volcano: this.volcano.get('vd_id'),
        selectingEruption: selectingEruption
      });
      /** Event listener **/
      this.listenTo(this.volcano, 'change', this.fetchEruptions);
      this.listenTo(this.collection, 'add', this.timeSeriesChanged);
      this.listenTo(this.collection, 'remove', this.timeSeriesChanged);
      this.render();
    },

    fetchEruptions: function() {
      this.eruptionSelect.fetchEruptions(this.volcano.get('vd_id'));
    },

    clear: function() {
      if (this.collection.length === 0) {
        this.overviewGraph.hide();
        this.eruptionSelect.hide();
        this.render();
      }
    },
    timeSeriesChanged: function() {
      if (this.collection.length == 0) {
        this.overviewGraph.hide();
        this.eruptionSelect.hide();
      }else{
        this.overviewGraph.show();
        this.eruptionSelect.show();
      }
      this.render();
    },

    render: function() {
      this.overviewGraph.$el.appendTo(this.$el);
      this.eruptionSelect.$el.appendTo(this.$el);
    }
  });
});