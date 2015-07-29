define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Eruption = require('models/eruption');

  return Backbone.Collection.extend({
    model: Eruption,
    
    initialize: function(vd_id) {
      if (vd_id)
        this.changeVolcano(vd_id);
    },

    changeVolcano: function(vd_id, handler) {
      this.url = 'api/?data=eruption_list&vd_id=' + vd_id;
      this.fetch().done(handler);
    },
    getAvailableEruptions: function(timeRange){
      
      
      if(timeRange == undefined){
        return this.models;
      }else{
        
        var startTime = timeRange.get('startTime');
        var endTime = timeRange.get('endTime');
        for(var i=0;i<this.models.length;i++){
          if(this.models[i].get('ed_stime')<endTime && this.models[i].get('ed_stime')> startTime){
            result.push(this.models[i]);
          }
        }
        return result;
      }
    }
  });
});
