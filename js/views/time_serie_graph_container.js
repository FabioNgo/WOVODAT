define(function(require) {
	'use strict';
	var $ = require('jquery'),
  Backbone = require('backbone'),
  _ = require('underscore');

  var TimeSerieGraph = require("views/time_serie_graph"); 

  return Backbone.View.extend({
  	el: '',
  	initialize : function(options) {
  		this.selectingTimeSeries = options.selectingTimeSeries;
  		

  		this.filterObserver = options.filterObserver;
  		// this.listenTo( this.selectingFilter, "add", this.addGraph );
  		// this.listenTo( this.selectingFilter, "remove", this.removeGraph );

  		this.graphs = [];
  	},

  	// addGraph : function( filter ) {
  	// 	var val = filter.get("filter");

  	// 	this.graphs[val] = new TimeSerieGraph( {
  	// 		timeRange : this.timeRange,
  	// 		model : this.model,
  	// 		filter : filter
  	// 	});
  	// 	this.$el.append(this.graphs[val].$el);

  	// 	this.graphs[val].filter.trigger("change");

  	// 	this.filterObserver.trigger("filter-change");
  	// },
    addGraph : function( timeSerie ) {
      // var val = filter.get("filter");
      // selectingTimeSeries.
      // timeSerie.fetch({
      //   success: function(collection, response) {
      //     // console.log(e);
      //     console.log(response);

      //   }
      // });
      var timeSerieGraph = new TimeSerieGraph( {
        // timeRange : this.timeRange,
        timeSerie: timeSerie
      });
      this.graphs.push(timeSerieGraph);
      

      // this.graphs[val].filter.trigger("change");

      // this.filterObserver.trigger("filter-change");
    },

  	// removeGraph : function( timeSerie ) {
  	// 	// var val = filter.get("filter");
  	// 	// this.graphs[val].destroy();
   //    for (var i = 0; i < this.graphs.length; i++) {
   //      if(this.graphs[i].timeSerie.id == timeSerie.id){
   //        this.graphs[i].destroy();
   //        this.graphs.splice(i,i+1); //remove graph
   //        break;
   //      }
   //    };
  	// 	// this.filterObserver.trigger("filter-change");
  	// },
    timeRangeChanged: function(timeRange){
      for (var i = 0; i < this.graphs.length; i++) {
        this.graphs[i].timeRangeChanged(timeRange);
      };
    },
    selectingTimeSerieChanged: function(selectingTimeSeries){
      this.graphs.length =0;
      this.$el.html("");
      for (var i = 0; i < selectingTimeSeries.models.length; i++) {
        this.addGraph(selectingTimeSeries.models[i]);
      };
    },
    // render: function(selectingTimeSeries) {
    //   this.overviewGraph.$el.appendTo(this.$el);
    // },
    hide: function(){
      this.$el.html("");
    },
    show: function(){
      for (var i = 0; i < this.graphs.length; i++) {
        this.$el.append(this.graphs[i].$el);
      };
    },
    destroy: function() {
      // From StackOverflow with love.
      //console.log("destroy");
      for(var g in this.graphs) {
      	if (this.graphs.hasOwnProperty(g)) {
            this.graphs[g].destroy();
        }
      }
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }

  });

});