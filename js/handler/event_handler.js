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
        'makeOffline',
        'timeSeriesChanged',
        // 'onAddSelectingTimeSeries',
        // 'onRemoveSelectingTimeSeries',
        // 'onResetSelectingTimeSeries',
        'timeSeriesChanged',
        'filtersSelectChange',
        'selectingTimeSeriesChanged',
        'selectingTimeSeriesChangedCheck',
        'changeSelectingEruptions',
        'serieGraphTimeRangeChanged',
        'selectingTimeRangeChanged',
        'selectingFiltersChanged',
        'eruptionTimeRangeChanged',
        'eruptionsFetched',
        'timeSeriesSelectHidden',
        'filtersSelectHidden',
        'overviewGraphHidden',
        'eruptionSelectHidden',
        'eruptionGraphHidden',
        'eruptionGraphShown',
        'highlightOverViewGraphChanged'

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
      this.eruptionTimeRange = options.eruptionTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
      this.selectingTimeRange = options.selectingTimeRange;
      this.filtersSelect = options.filtersSelect;
      this.selectingFilters = options.selectingFilters;
      this.eruptionForecastsGraph = options.eruptionForecastsGraph;
      this.eruptions = options.eruptions,
      this.offline = options.offline,
      //event listeners
      // this.listenTo(this.volcanoSelect,'change',this.onSelectVolcanoChanged)
      this.listenTo(this.selectingVolcano, 'update', this.changeVolcano);
      this.listenTo(this.volcanoSelect,'make-offline', this.makeOffline);
      // this.listenTo(this.selectingTimeSeries, 'syncAll', this.onAddSelectingTimeSeries);
      // this.listenTo(this.selectingTimeSeries,'remove', this.onRemoveSelectingTimeSeries);
      this.listenTo(this.timeSeries,'loaded', this.timeSeriesChanged);
      this.listenTo(this.timeSeriesSelect,"filter-select-change", this.filtersSelectChange);
      // this.listenTo(this.selectingTimeSeries,'reset', this.onResetSelectingTimeSeries);
      
//this.listenTo(this.selectingTimeSeries, 'getdata', this.updateTimeSeriesData);
      this.listenTo(this.selectingTimeSeries, 'allLoaded', this.selectingTimeSeriesChanged);
      //when each selecting model fetched success, trigger this event
      this.listenTo(this.selectingTimeSeries, 'change', this.selectingTimeSeriesChangedCheck);
      this.listenTo(this.selectingEruptions, 'add', this.changeSelectingEruptions);
      this.listenTo(this.serieGraphTimeRange,'update',this.serieGraphTimeRangeChanged);
      this.listenTo(this.forecastsGraphTimeRange,'update',this.forecastsGraphTimeRangeChanged);
      this.listenTo(this.selectingTimeRange,'update',this.selectingTimeRangeChanged);
      this.listenTo(this.serieGraphTimeRange,'zoom',this.highlightOverViewGraphChanged);
      this.listenTo(this.selectingFilters,'update',this.selectingFiltersChanged);
      this.listenTo(this.eruptionTimeRange,'update',this.eruptionTimeRangeChanged);
      this.listenTo(this.eruptions,'fetched',this.eruptionsFetched);
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
      this.selectingTimeSeries.reset();
      this.selectingTimeSeriesChanged();
      //this.eruptionSelect.show();
    },
    timeSeriesChanged: function(e){
      this.timeSeriesSelect.timeSeriesChanged(this.timeSeries);
    },
   
    // updateTimeSeriesData: function(e){
    //   var vd_id = this.selectingVolcano.get('vd_id');
    //   this.timeSeriesSelect.updateVolcanoData(vd_id, this.timeSeries);
    // },
    makeOffline: function(e){
      this.offline.makeOffline(this.selectingVolcano);
    },
    filtersSelectChange : function(e){
      this.filtersSelect.showGraph();
    },
    selectingTimeSeriesChanged: function(e){
      
      this.filtersSelect.selectingTimeSeriesChanged(this.selectingTimeSeries); // filter is rendered out.

    },
    selectingTimeSeriesChangedCheck: function(e){
      // this.filtersSelect.showLoading();
      var allLoaded = true;
      // while(!allLoaded){
        for(var i=0;i<this.selectingTimeSeries.models.length;i++){
          var model = this.selectingTimeSeries.models[i];
          if(model.get('data') == undefined){
            allLoaded = false;
            break;
          }
        }
      // }
      if(allLoaded){
        this.selectingTimeSeries.trigger("allLoaded");
      }
      //   this.selectingTimeSeries.trigger("check");
      // }
    },
    selectingFiltersChanged: function(e){
      this.overviewGraphContainer.selectingFiltersChanged(this.selectingFilters);
      this.overviewGraph.selectingFiltersChanged(this.selectingFilters);
      this.timeSeriesGraphContainer.selectingFiltersChanged(this.selectingFilters);
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
      // this.eruptionSelect.hide();
      // this.eruptionSelect.show();
    },
    overviewGraphHidden: function(e){
      // this.eruptionSelect.hide();
      // this.eruptionSelect.show();
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
    eruptionTimeRangeChanged: function(e){
      this.eruptionGraph.eruptionTimeRangeChanged(this.eruptionTimeRange);
    },
    selectingTimeRangeChanged: function(e){
      
      this.eruptionSelect.selectingTimeRangeChanged(this.selectingTimeRange);
      this.serieGraphTimeRange.set({
        'startTime': this.selectingTimeRange.get('startTime'),
        'endTime': this.selectingTimeRange.get('endTime'),
        'overviewGraphMinX': this.selectingTimeRange.get('overviewGraphMinX'),
        'overviewGraphMaxX': this.selectingTimeRange.get('overviewGraphMaxX'),
      });
      // this.selectingTimeRange.trigger('update');
      this.serieGraphTimeRangeChanged();
      // this.timeSeriesGraphContainer.selectingTimeRangeChanged(e);
    },
    highlightOverViewGraphChanged: function(e){
      this.selectingTimeRange.set({
        selectedMinX: this.serieGraphTimeRange.get('minX'),
        selectedMaxX: this.serieGraphTimeRange.get('maxX')
      })
      // console.log(this.selectingTimeRange);
      this.overviewGraph.selectingRegionChanged(this.selectingTimeRange);
    },
    eruptionsFetched : function(e){
    	this.eruptionSelect.eruptionsFetched();
    },
    destroy: function() {
      
    }
  });
});