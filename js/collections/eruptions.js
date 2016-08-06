define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Eruption = require('models/eruption');

  return Backbone.Collection.extend({
    model: Eruption,
    
    initialize: function(options) {
      this.offline = options.offline;
    },

    changeVolcano: function(vd_id, handler) {
      if(this.offline){
        this.url = 'offline-data/eruption_list.json';
      }else{
        this.url = 'api/?data=eruption_list&vd_id=' + vd_id;
      }
      
      this.fetch({
        success: function(e){
          e.trigger("fetched");
        }
      });
    },
    getAvailableEruptions: function(timeRange){
      
      if(timeRange == undefined){
        // console.log(this.models);
        return this.models;
      }else{
        var result = [];
        var startTime = timeRange.get('startTime');
        var endTime = timeRange.get('endTime');
        for(var i=0;i<this.models.length;i++){
          if(this.models[i].get('ed_stime')<endTime && this.models[i].get('ed_stime')> startTime){
            result.push(this.models[i]);
          }
        }
        // console.log(this.models.length);
        return result;
      }
    }
  });
});
