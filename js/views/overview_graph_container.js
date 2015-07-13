define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      // Serie = require('models/serie'),
      // TimeSerieGraph = require('views/time_serie_graph'),
      
      TimeRange = require('models/time_range'),
      Eruption = require('models/eruption'),
      Eruptions = require('collections/eruptions'),
      EruptionSelect = require('views/eruption_select');


  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      /** Variable declaration **/
      this.overviewSelectingTimeRange = new TimeRange();
      this.observer = options.observer;
      this.overviewSelectingTimeSeries = options.selectingTimeSeries;
      this.overviewGraph = options.graph;
    },
     //hide overview graph from page
    hide: function(){
      this.$el.html("");
      this.trigger('hide');
    },

    //show overview graph on page
    show: function(){
      this.render();
    },
    selectingFiltersChanged: function(selectingFilters) {
      this.selectingFilters = selectingFilters;
      if (this.selectingFilters.length == 0) {
        this.hide();
      }else{
        this.show();
      }
    },

    render: function() {
      this.overviewGraph.$el.appendTo(this.$el);
    }
  });
});