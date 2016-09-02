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
    	this.filterAttributes = [];
    	this.filterAttributes.push({name:filter});
    	this.isChecked = false;
    	// console.log(this);
    },
    addFilter: function(filter){
      for(var i=0;i<this.filterAttributes.length;i++){
        var filterAttributes = this.filterAttributes[i];
        if(filterAttributes.name == filter){
          return;
        }
      }
  		this.filterAttributes.push({name:filter});
    },
    removeFilter: function(filter){
      var index = -1;
      for(var i=0;i<this.filterAttributes.length;i++){
        var filterAttributes = this.filterAttributes[i];
        if(filterAttributes.name == filter){
          index = i;
        }
      }
    	
    	if(index > -1){
		    this.filterAttributes.splice(index, 1);
    	}
    }
      /** return timeSerie with respective filter**/

  });
});