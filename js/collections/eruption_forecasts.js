define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      EruptionForecast = require('models/eruption_forecast');

  return Backbone.Collection.extend({
    model: EruptionForecast,
    
    initialize: function(options) {
      this.offline = options.offline;
    },

    changeVolcano: function(vd_id) {
      if(this.offline){
        this.url = 'offline-data/eruption_forecast.json';
      }else{
        this.url = 'api/?data=eruption_forecast_list&offline=false&vd_id=' + vd_id;
      }
      
      this.fetch();
    }
  });
});
