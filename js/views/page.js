define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Pace = require('pace'),
      template = require('text!templates/page.html'),
      Volcano = require('models/volcano'),
      Volcanoes = require('collections/volcanoes'),
      VolcanoSelect = require('views/volcano_select'),
      Eruption = require('models/eruption'),
      Eruptions = require('collections/eruptions'),
      EruptionSelect = require('views/eruption_select'),
      EruptionGraph = require('views/eruption_graph'),
      EruptionForecasts = require('collections/eruption_forecasts'),
      EruptionForecastGraph = require('views/eruption_forecast_graph'),
      TimeSeries = require('collections/time_series'),
      TimeSeriesSelect = require('views/time_series_select'),   
      
      Filter = require('models/filter'),
      FilterSelect = require('views/filter_select'),
      Filters = require('collections/filters'),
      
      TimeRange = require('models/time_range'),
      SelectingTimeSeries = require('collections/selecting_time_series'),
      TimeSeriesContainer = require('views/time_series_container'),
      UrlLoader = require('models/url_loader');

  return Backbone.View.extend({
    el: '#main',
    
    initialize: function() {
      this.render();
    },
    render: function() {
      var eruptions = new Eruptions(),
          observer = new (Backbone.Model.extend())(),
          timeRange = new TimeRange(),
          selectingTimeSeries = new SelectingTimeSeries(),
          volcanoes = new Volcanoes(),
          selectingVolcano = new Volcano(),
          selectingEruption = new Eruption();
      
      new VolcanoSelect({
        collection: volcanoes,
        observer: observer,
        selectingVolcano: selectingVolcano
      }).$el.appendTo(this.$el);

      new EruptionSelect({
        collection: eruptions,
        observer: observer,
        volcano: selectingVolcano,
        selectingEruption: selectingEruption
      }).$el.appendTo(this.$el);

      new EruptionGraph({
        collection: eruptions,
        observer: observer,
        timeRange: timeRange
      }).$el.appendTo(this.$el);

      new EruptionForecastGraph({
        collection: new EruptionForecasts(),
        observer: observer,
        timeRange: timeRange,
        volcano: selectingVolcano
      }).$el.appendTo(this.$el);

      new TimeSeriesSelect({
        collection: new TimeSeries(),
        observer: observer,
        selectingTimeSeries: selectingTimeSeries,
        volcano: selectingVolcano
      }).$el.appendTo(this.$el);

      new TimeSeriesContainer({
        observer: observer,
        timeRange: timeRange,
        collection: selectingTimeSeries
      }).$el.appendTo(this.$el);

      new UrlLoader({
        observer: observer,
        volcanoes: volcanoes,
        selectingVolcano: selectingVolcano,
        eruptions: eruptions,
        selectingEruption: selectingEruption
      }).$el.appendTo(this.$el);
    }
  });
});