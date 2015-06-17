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
        'onAddSelectingTimeSeries',
        'onRemoveSelectingTimeSeries',
        'onResetSelectingTimeSeries',
        'timeSeriesChanged',
        'changeEruption'
      );
      //Variable declaration
      this.volcanoSelect = options.volcanoSelect;
      this.timeSeriesSelect = options.timeSeriesSelect;
      this.overviewGraphContainer = options.overviewGraphContainer;
      this.eruptionSelect = options.eruptionSelect;
      this.selectingVolcano = options.selectingVolcano;
      this.selectingEruption = options.selectingEruption;
      this.selectingTimeSeries = options.selectingTimeSeries;
      this.timeSeries = options.timeSeries;
      //event listeners
      // this.listenTo(this.volcanoSelect,'change',this.onSelectVolcanoChanged)
      this.listenTo(this.selectingVolcano, 'change', this.changeVolcano);
      this.listenTo(this.selectingTimeSeries, 'add', this.onAddSelectingTimeSeries);
      this.listenTo(this.selectingTimeSeries,'remove', this.onRemoveSelectingTimeSeries);
      this.listenTo(this.selectingTimeSeries,'reset', this.onResetSelectingTimeSeries);
      this.listenTo(this.timeSeries, 'sync', this.timeSeriesChanged);
      this.listenTo(this.selectingEruption, 'change', this.changeEruption);

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
    
    onAddSelectingTimeSeries: function(e) {
      this.selectingTimeSeries.onAdd(e);
      this.selectingTimeSeriesChange(); 
    },

    onRemoveSelectingTimeSeries: function(e) {
      this.selectingTimeSeries.onRemove(e);
      this.selectingTimeSeriesChange();
    },

    onResetSelectingTimeSeries: function(e) {
      this.selectingTimeSeriesChange();
    },

    selectingTimeSeriesChange: function(e){
      this.overviewGraphContainer.timeSeriesChanged(this.selectingTimeSeries);
      this.eruptionSelect.timeSeriesChanged(this.selectingTimeSeries);
    },

    timeSeriesChanged: function(e){
      this.timeSeriesSelect.render(this.timeSeries);
      this.selectingTimeSeries.reset();
    },

    changeEruption: function(e){
      this.eruptionSelect.changeEruption(this.selectingEruption);
    },

    destroy: function() {
      
    }
  });
});