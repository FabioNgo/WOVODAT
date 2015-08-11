/** 
  EventHandler: handle all event of all elements in page
  Author: Fabio 17-Jun-2015
**/
define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore');

  return Backbone.Model.extend({
    
    initialize: function(options) {
      _(this).bindAll(
        // 'onSelectVolcanoChanged',
        'changeVolcano',
        'timeSeriesChanged',
        // 'onAddSelectingTimeSeries',
        // 'onRemoveSelectingTimeSeries',
        // 'onResetSelectingTimeSeries',
        'timeSeriesChanged',
        'selectingTimeSeriesChanged',
        'changeSelectingEruptions',
        'serieGraphTimeRangeChanged',
        'selectingTimeRangeChanged',
        'selectingFiltersChanged',
        'timeSeriesSelectHidden',
        'filtersSelectHidden',
        'overviewGraphHidden',
        'eruptionSelectHidden',
        'eruptionGraphHidden',
        'eruptionGraphShown'

      );
      //Variable declaration
      this.volcanoSelect = options.volcanoSelect;
      this.timeSeriesSelect = options.timeSeriesSelect;
      this.overviewGraphContainer = options.overviewGraphContainer;
      this.eruptionSelect = options.eruptionSelect;
      this.selectingVolcano = options.selectingVolcano;
      this.selectingEruptions = options.selectingEruptions;
      this.selectingTimeSeries = options.selectingTimeSeries;
      this.timeSeries = options.timeSeries;
      this.overviewGraph = options.overviewGraph;
      this.eruptionGraph = options.eruptionGraph;
      this.timeSeriesGraphContainer = options.timeSeriesGraphContainer;
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
      this.selectingTimeRange = options.selectingTimeRange;
      this.filtersSelect = options.filtersSelect;
      this.selectingFilters = options.selectingFilters;
      this.eruptionForecastsGraph = options.eruptionForecastsGraph;
      //event listeners
      // this.listenTo(this.volcanoSelect,'change',this.onSelectVolcanoChanged)
      this.listenTo(this.selectingVolcano, 'change', this.changeVolcano);
      // this.listenTo(this.selectingTimeSeries, 'syncAll', this.onAddSelectingTimeSeries);
      // this.listenTo(this.selectingTimeSeries,'remove', this.onRemoveSelectingTimeSeries);
      this.listenTo(this.timeSeries,'sync', this.timeSeriesChanged);
      // this.listenTo(this.selectingTimeSeries,'reset', this.onResetSelectingTimeSeries);
      this.listenTo(this.timeSeries, 'sync', this.timeSeriesChanged);
      this.listenTo(this.selectingTimeSeries, 'update', this.selectingTimeSeriesChanged);
      this.listenTo(this.selectingEruptions, 'add', this.changeSelectingEruptions);
      this.listenTo(this.serieGraphTimeRange,'update',this.serieGraphTimeRangeChanged);
      this.listenTo(this.forecastsGraphTimeRange,'update',this.forecastsGraphTimeRangeChanged);
      this.listenTo(this.selectingTimeRange,'update',this.selectingTimeRangeChanged);
      this.listenTo(this.selectingFilters,'update',this.selectingFiltersChanged);
      /**
      * Events when some part is hidden
      */
      this.listenTo(this.timeSeriesSelect,'hide',this.timeSeriesSelectHidden);
      this.listenTo(this.filtersSelect,'hide',this.filtersSelectHidden);
      this.listenTo(this.overviewGraphContainer,'hide',this.overviewGraphHidden);

      this.listenTo(this.eruptionSelect,'hide',this.eruptionSelectHidden);
      this.listenTo(this.eruptionGraph,'hide',this.eruptionGraphHidden);
      this.listenTo(this.eruptionGraph,'show',this.eruptionGraphShown);


      // this.listenTo(this.selectingEruptions, 'reset', this.resetSelectingEruptions);

    },
    // onSelectVolcanoChanged: function(e){
    //   this.volcanoSelect.onSelectChanged(this.selectingVolcano);
    // },

    changeVolcano: function(e) {
      var vd_id = this.selectingVolcano.get('vd_id');
      this.volcanoSelect.changeSelection(vd_id);
      this.timeSeriesSelect.changeVolcano(vd_id,this.timeSeries);
      // this.selectingTimeSeries.reset();
      this.eruptionSelect.fetchEruptions(vd_id);
    },
    timeSeriesChanged: function(e){
      this.timeSeriesSelect.timeSeriesChanged(this.timeSeries);
    },
   

    

    selectingTimeSeriesChanged: function(e){
      
      this.filtersSelect.selectingTimeSeriesChanged(this.selectingTimeSeries);
    },
    selectingFiltersChanged: function(e){

      this.overviewGraphContainer.selectingFiltersChanged(this.selectingFilters);
      this.overviewGraph.selectingFiltersChanged(this.selectingFilters);
      this.timeSeriesGraphContainer.selectingFiltersChanged(this.selectingFilters);
      this.eruptionSelect.selectingFiltersChanged(this.selectingFilters);
      
    },
    timeSeriesChanged: function(e){
      this.timeSeriesSelect.render(this.timeSeries);
      if(this.selectingTimeSeries.length==0){
        this.selectingTimeSeries.reset();
      }
      
    },

    changeSelectingEruptions: function(e){
      // this.eruptionSelect.changeEruption(this.selectingEruption);
      this.eruptionGraph.changeEruption(e);
      this.eruptionForecastsGraph.changeEruption(e);
    },
    timeSeriesSelectHidden: function(e){
      this.filtersSelect.hide();
      
    },
    filtersSelectHidden: function(e){
      this.overviewGraph.hide();
      this.eruptionSelect.hide();
    },
    overviewGraphHidden: function(e){
      this.eruptionSelect.hide();

    },
    eruptionGraphHidden: function(e){
      this.timeSeriesGraphContainer.hide();
      this.eruptionForecastsGraph.hide();

    },
    eruptionGraphShown: function(e){
      this.timeSeriesGraphContainer.show();
      this.eruptionForecastsGraph.show();

    },
    eruptionSelectHidden: function(e){

      this.eruptionGraph.hide();
      
    },
    serieGraphTimeRangeChanged: function(e){

      this.timeSeriesGraphContainer.serieGraphTimeRangeChanged(this.serieGraphTimeRange);
    },
    forecastsGraphTimeRangeChanged: function(e){

      this.eruptionForecastsGraph.forecastsGraphTimeRangeChanged(this.forecastsGraphTimeRange);
    },
    selectingTimeRangeChanged: function(e){
      
      this.eruptionSelect.selectingTimeRangeChanged(this.selectingTimeRange);
      this.serieGraphTimeRange.set({
        'startTime': this.selectingTimeRange.get('startTime'),
        'endTime': this.selectingTimeRange.get('endTime'),
      });
      // this.selectingTimeRange.trigger('update');
      this.serieGraphTimeRangeChanged();
      // this.timeSeriesGraphContainer.selectingTimeRangeChanged(e);
    },
    destroy: function() {
      
    }
  });
});