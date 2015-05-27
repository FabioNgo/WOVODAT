define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'sr_id',

    initialize: function() {
      this.url = 'api/?data=time_serie&sr_id=' + this.get("sr_id");
    },
    getDisplayName : function() {

    	var x =  this.get("data_type")  
          + ( this.has('component') ? '-' + this.get('component') : ""  )
          + ( this.has('station_code') ?  " (" + this.get('station_code') + ")" : ""  );
      //console.log(x);
      //console.log( this.has('station_code') );

      return x;
    }
  });
});