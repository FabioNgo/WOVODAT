define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    initialize: function() {
      this.set({
        selectings: [],
        MAX_N_SERIES: 3
      });
    },

    select: function(sr_id) {
      if (this.get('selectings').indexOf(sr_id) === -1 && this.get('selectings').length < this.get('MAX_N_SERIES')) {
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