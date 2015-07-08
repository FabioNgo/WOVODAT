define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'filter',
    //Note: single filter only
    initialize: function(timeSerie,filter) {
    	this.timeSerie = timeSerie;
    	this.name = [];
    },
    addFilter: function(filter){
    	if(this.name.indexOf(filter) == -1){
    		this.name.push(filter);
    	}
    	
    	
    }
  });
});