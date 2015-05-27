define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      EruptionForecast = require('models/eruption_forecast');

  return Backbone.Collection.extend({
    model: EruptionForecast,
    
    initialize: function(vd_id) {
      if (vd_id)
        this.changeVolcano(vd_id);
    },

    changeVolcano: function(vd_id, handler) {
      this.url = 'api/?data=eruption_forecast_list&vd_id=' + vd_id;
      this.fetch().done(handler);
    }
  });
});
