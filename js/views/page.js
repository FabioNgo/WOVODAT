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
      TimeSeriesSelect = require('views/time_series_select'),
      Eruption = require('models/eruption'),
      Eruptions = require('collections/eruptions'),
      EruptionSelect = require('views/eruption_select'),
      EruptionGraph = require('views/eruption_graph'),
      EruptionForecasts = require('collections/eruption_forecasts'),
      EruptionForecastsGraph = require('views/eruption_forecast_graph'),
      TimeSerie = require('models/serie'),
      // TimeSeriesSelect = require('views/time_series_select'),
      OverviewGraphContainer = require('views/overview_graph_container'),
      OverviewGraph = require('views/overview_graph'),
      Filter = require('models/filter'),
      FilterSelect = require('views/filter_select'),
      Filters = require('collections/filters'),
      TimeRange = require('models/time_range'),
      TimeSeries = require('collections/series'),
      TimeSeriesContainer = require('views/time_series_container'),
      Tooltip = require('views/series_tooltip'),
      TimeSeriesGraphContainer = require('views/time_serie_graph_container'),
      EventHandler = require('handler/event_handler'),
      Offline = require('views/offline');

  return Backbone.View.extend({
    el: '#main',
    
    initialize: function(selecting_vd_num,ed_stime_num,ed_etime_num,selectedTimeSeries) {
      this.selecting_vd_num = selecting_vd_num;
      this.ed_stime_num = ed_stime_num;
      this.ed_etime_num = ed_etime_num;
      this.selectedTimeSeries = selectedTimeSeries;
      this.$el.html("");
      this.render();
    },
    render: function() {
      /**
      * Variables declaration
      **/
      var 
          observer = new (Backbone.Model.extend())(),
          categories=["Seismic","Deformation","Gas","Hydrology","Thermal","Fields","Meteology"],
          selectingTimeSeries = new TimeSeries(),
          selectingFilters = new Filters(),
          volcanoes = new Volcanoes(),
          selectingEruptions = new Eruptions(),
          eruptions = new Eruptions(),
          eruptionForecasts = new EruptionForecasts,
          selectingVolcano = new Volcano(),
          timeSeries = new TimeSeries(),
          serieGraphTimeRange = new TimeRange(),
          forecastsGraphTimeRange = new TimeRange(),
          selectingTimeRange = new TimeRange(),
          eruptionTimeRange = new TimeRange(),
          overviewGraphTimeRange = new TimeRange(),


          volcanoSelect = new VolcanoSelect({
            collection: volcanoes,
            categories: categories,
            selectingVolcano: selectingVolcano,
            selecting_vd_num: this.selecting_vd_num
          }),

          timeSeriesSelect = new TimeSeriesSelect({
            categories: categories,
            volcano: selectingVolcano,
            selectingTimeSeries: selectingTimeSeries,
            selectedTimeSeries: this.selectedTimeSeries,
            timeSeries: timeSeries,
            selectingFilters: selectingFilters
          }),
          filtersSelect = new FilterSelect({
            categories: categories,
            selectings: selectingTimeSeries,
            selectingFilters: selectingFilters
          }),
          overviewGraph = new OverviewGraph({
            categories: categories,
            selectingTimeSeries: this.overviewSelectingTimeSeries,
            serieGraphTimeRange: serieGraphTimeRange,
            selectingTimeRange: selectingTimeRange,
            overviewGraphTimeRange: overviewGraphTimeRange,
            // collection: filterColorCollection
          }),

          overviewGraphContainer = new OverviewGraphContainer({
            categories: categories,
            selectingTimeSeries: selectingTimeSeries,
            serieGraphTimeRange: serieGraphTimeRange,
            observer: observer,
            graph: overviewGraph
          }),

          eruptionSelect = new EruptionSelect({
            categories: categories,
            eruptions: eruptions,
            eruptionForecasts: eruptionForecasts,
            observer: observer,
            selectingEruptions: selectingEruptions,
            selecting_vd_num: this.selecting_vd_num,
            ed_stime_num: this.ed_stime_num,
            ed_etime_num: this.ed_etime_num,
            selectingTimeRange: selectingTimeRange,
          }),

          

          eruptionGraph = new EruptionGraph({
            //eruptions: eruptions,
            observer: observer,
            categories: categories,
            serieGraphTimeRange: serieGraphTimeRange,
            forecastsGraphTimeRange: forecastsGraphTimeRange,
            eruptionTimeRange: eruptionTimeRange,
            overviewGraphTimeRange: overviewGraphTimeRange,
            selecting_vd_num: this.selecting_vd_num,
            ed_stime_num: this.ed_stime_num,
            ed_etime_num: this.ed_etime_num
          }),

          eruptionForecastsGraph = new EruptionForecastsGraph({
            observer: observer,
            categories: categories,
            eruptionForecasts: eruptionForecasts

          }),
          timeSeriesGraphContainer = new TimeSeriesGraphContainer({
            observer: observer,
            categories: categories,
            selectingTimeSeries: selectingTimeSeries,
            eruptionTimeRange: eruptionTimeRange,
            serieGraphTimeRange: serieGraphTimeRange,
            forecastsGraphTimeRange: forecastsGraphTimeRange,
            // timeRange: timeRange

          }),

          // urlLoader = new UrlLoader({
          //   observer: observer,
          //   volcanoes: volcanoes,
          //   eruptions: eruptions,
          //   selectingEruptions: selectingEruptions
          // }),
          offline = new Offline({
            selectingVolcano : selectingVolcano
          }),


          eventHandler = new EventHandler({
            categories: categories,
            volcanoSelect: volcanoSelect,
            timeSeriesSelect: timeSeriesSelect,
            filtersSelect: filtersSelect,
            overviewGraphContainer: overviewGraphContainer,
            eruptionSelect: eruptionSelect,
            selectingVolcano: selectingVolcano,
            selectingEruptions: selectingEruptions,
            selectingTimeSeries: selectingTimeSeries,
            timeSeries :timeSeries,
            overviewGraph: overviewGraph,
            eruptionGraph: eruptionGraph,
            eruptionTimeRange: eruptionTimeRange,
            timeSeriesGraphContainer: timeSeriesGraphContainer,
            serieGraphTimeRange: serieGraphTimeRange,
            forecastsGraphTimeRange: forecastsGraphTimeRange,
            selectingTimeRange: selectingTimeRange,
            selectingFilters: selectingFilters,
            eruptionForecastsGraph: eruptionForecastsGraph,
            eruptions: eruptions,
            offline: offline,
          });
          //console.log(volcanoes);
          // console.log(filterColorCollection);
          // console.log(overviewGraph);
      /** Body **/
      // var test = new TimeSerie('58166f4b40cca4e8ed2522b5f00bc756');
      // test.fetch({
      //   success: function(collection, response) {
      //     console.log(response);
      //   }
      // });
      volcanoSelect.$el.appendTo(this.$el);
      offline.$el.appendTo(this.$el);
      timeSeriesSelect.$el.appendTo(this.$el);
      filtersSelect.$el.appendTo(this.$el);
      overviewGraphContainer.$el.appendTo(this.$el);
      eruptionSelect.$el.appendTo(this.$el);
      eruptionGraph.$el.appendTo(this.$el);
      eruptionForecastsGraph.$el.appendTo(this.$el);
      timeSeriesGraphContainer.$el.appendTo(this.$el);
      // urlLoader.$el.appendTo(this.$el);

      // new EruptionForecastGraph({
      //   collection: new EruptionForecasts(),
      //   observer: observer,
      //   timeRange: timeRange,
      //   volcano: selectingVolcano
      // }).$el.appendTo(this.$el);


    }
  });
});