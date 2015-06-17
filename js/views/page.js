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
      OverviewGraphContainer = require('views/overview_graph_container'),
      Filter = require('models/filter'),
      FilterSelect = require('views/filter_select'),
      Filters = require('collections/filters'),
      
      TimeRange = require('models/time_range'),
      SelectingTimeSeries = require('collections/selecting_time_series'),
      TimeSeriesContainer = require('views/time_series_container'),
      EventHandler = require('handler/event_handler'),
      UrlLoader = require('models/url_loader');

  return Backbone.View.extend({
    el: '#main',
    
    initialize: function() {
      this.render();
    },
    render: function() {
      /**
      * Variables declaration
      **/
      var 
          observer = new (Backbone.Model.extend())(),
          timeRange = new TimeRange(),
          selectingTimeSeries = new SelectingTimeSeries(),
          volcanoes = new Volcanoes(),
          selectingEruption = new Eruption(),
          eruptions = new Eruptions(),
          selectingVolcano = new Volcano(),
          timeSeries = new TimeSeries(),
          volcanoSelect = new VolcanoSelect({
            collection: volcanoes,
            observer: observer,
            selectingVolcano: selectingVolcano
          }),

          timeSeriesSelect = new TimeSeriesSelect({
            observer: observer,
            volcano: selectingVolcano,
            selectings: selectingTimeSeries
          }),

          overviewGraphContainer = new OverviewGraphContainer({
            collection: selectingTimeSeries,
            observer: observer,
            timeRange: timeRange
          }),

          eruptionSelect = new EruptionSelect({
            collection: eruptions,
            observer: this.observer
          }),

          // timeSeriesContainer = new TimeSeriesContainer({
          //   observer: observer,
          //   timeRange: timeRange
          // }),

          // eruptionGraph = new EruptionGraph({
          //   collection: eruptions,
          //   observer: observer,
          //   timeRange: timeRange
          // }),

          urlLoader = new UrlLoader({
            observer: observer,
            volcanoes: volcanoes,
            eruptions: eruptions,
            selectingEruption: selectingEruption
          }),

          eventHandler = new EventHandler({
            volcanoSelect: volcanoSelect,
            timeSeriesSelect: timeSeriesSelect,
            overviewGraphContainer: overviewGraphContainer,
            eruptionSelect: eruptionSelect,
            selectingVolcano: selectingVolcano,
            selectingEruption: selectingEruption,
            selectingTimeSeries: selectingTimeSeries,
            timeSeries :timeSeries
          });
      /** Body **/
      volcanoSelect.$el.appendTo(this.$el);
      timeSeriesSelect.$el.appendTo(this.$el);
      overviewGraphContainer.$el.appendTo(this.$el);
      eruptionSelect.$el.appendTo(this.$el);

      urlLoader.$el.appendTo(this.$el);
      // new EruptionForecastGraph({
      //   collection: new EruptionForecasts(),
      //   observer: observer,
      //   timeRange: timeRange,
      //   volcano: selectingVolcano
      // }).$el.appendTo(this.$el);


    }
  });
});