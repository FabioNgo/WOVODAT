define(function(require) {
	'use strict';
	var $ = require('jquery'),
  Backbone = require('backbone'),
  _ = require('underscore');

  var TimeSerieGraph = require("views/time_serie_graph"); 

  return Backbone.View.extend({
  	el: '',
  	initialize : function(options) {
  		this.selectingFilter = options.selectingFilter;
  		this.timeRange = options.timeRange;
  		this.filterObserver = options.filterObserver;
  		this.listenTo( this.selectingFilter, "add", this.addGraph );
  		this.listenTo( this.selectingFilter, "remove", this.removeGraph );

  		this.graphs = {};
  	},

  	addGraph : function( filter ) {
  		var val = filter.get("filter");

  		this.graphs[val] = new TimeSerieGraph( {
  			timeRange : this.timeRange,
  			model : this.model,
  			filter : filter
  		});

  		this.$el.append(this.graphs[val].$el);

  		this.graphs[val].filter.trigger("change");

  		this.filterObserver.trigger("filter-change");
  	},

  	removeGraph : function( filter ) {
  		var val = filter.get("filter");
  		this.graphs[val].destroy();

  		this.filterObserver.trigger("filter-change");
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