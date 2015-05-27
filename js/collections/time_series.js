define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Eruption = require('models/eruption');

  return Backbone.Collection.extend({
    initialize: function() {
    },

    changeVolcano: function(vd_id, handler) {
      this.url = 'api/?data=time_series_list&vd_id=' + vd_id;
      this.fetch().done(handler);
    }
  });
});