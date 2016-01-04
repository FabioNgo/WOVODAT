define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';
  /* Format
  	filter = {
  				timeSerie: timeSerie,
  				name = []
  				}
	*/
  return Backbone.Model.extend({
    idAttribute: 'filter',
    //Note: single filter only
    initialize: function(timeSerie,filter) {
    	this.timeSerie = timeSerie;
    	this.name = [];
    	this.name.push(filter);
    	// console.log(this);
    },
    addFilter: function(filter){
    	if(this.name.indexOf(filter) == -1){
    		this.name.push(filter);
    	}
    },
    removeFilter: function(filter){
    	var index = this.name.indexOf(filter);
    	if(index > -1){
		    this.name.splice(index, 1);
    	}
    },
    /** return timeSerie with respective filter**/

  });
});