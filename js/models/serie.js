define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'sr_id',

    initialize: function(sr_id) {
      this.set({
        sr_id: sr_id
      });
      this.url = 'api/?data=time_serie&sr_id=' + sr_id;
    },
    getName: function(){
      return ""+this.get('category')+" - " + this.get('station_code')+" (" + this.get('component') +")";
    }

  });
});