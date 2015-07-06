define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Serie = require('models/serie'),
      TimeSerieGraphContainer = require('views/time_serie_graph_container'),
      OverviewGraph = require('views/overview_graph'),
      FilterSelect = require('views/filter_select'),
      SelectingFilter = require('collections/selecting_filter'),      
      TimeRange = require('models/time_range');

  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      _(this).bindAll('addSerie', 'removeSerie', 'clear');

      this.timeRange = options.timeRange;
      this.overviewSelectingTimeRange = new TimeRange();

      this.filter_select = {};
      this.graphs = {};
      this.observer = options.observer;

      this.listenTo(this.collection, 'reset', this.clear);

      this.listenTo(this.collection, 'add', this.addSerie);
      this.listenTo(this.collection, 'remove', this.removeSerie);

      this.render();
    },

    clear: function() {
      //console.log("clear");
      if (this.collection.length === 0) {
        this.overviewGraph.destroy();
        for (var g in this.graphs) {
          if (this.graphs.hasOwnProperty(g)) {
            this.graphs[g].destroy();
            this.filter_select[g].destroy();
          }
        }
        this.graphs = {};
        this.render();
      }
    },

    addSerie: function(model) {

      var sr_id = model.get("sr_id");

      model.set("selectingFilter" ,  new SelectingFilter() );      
      
      this.filter_select[sr_id] = new FilterSelect({
        model: this.collection.get(sr_id),
        selectingFilter : model.get("selectingFilter"),
        filterObserver : this.observer
      });

      this.graphs[sr_id] = new TimeSerieGraphContainer({
        model: this.collection.get(sr_id),
        selectingFilter : model.get("selectingFilter"),
        timeRange: this.overviewSelectingTimeRange,
        filterObserver : this.observer
      });  

      this.collection.get(sr_id).fetch();
      
      this.$el.append(this.filter_select[sr_id].$el);
      this.$el.append(this.graphs[sr_id].$el);
    },

    removeSerie: function(model) {
      var sr_id = model.get("sr_id");
      this.filter_select[sr_id].destroy();    
      this.graphs[sr_id].destroy();
    },

    render: function() {
      this.overviewGraph = new OverviewGraph({
        collection: this.collection,
        timeRange: this.timeRange,
        selectingTimeRange: this.overviewSelectingTimeRange,
        filterObserver : this.observer
      });
      this.overviewGraph.$el.appendTo(this.$el);
    }
  });
});