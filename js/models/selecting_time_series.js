define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    initialize: function() {
      this.set({
        selectings: []
          //MAX_N_SERIES:
      });
    },
    // @params: sr_id: id of time serie
    select: function(sr_id) {
      
      if (this.get('selectings').indexOf(sr_id) === -1 ) {
        this.get('selectings').push(sr_id);
        this.trigger('select', sr_id);
      }
    },

    deselect: function(sr_id) {
      var id = this.get('selectings').indexOf(sr_id);
      if (id !== -1) {
        this.get('selectings').splice(id, 1);
        this.trigger('deselect', sr_id);
      }
    }
  });
});