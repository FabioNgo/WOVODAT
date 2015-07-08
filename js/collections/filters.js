define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),

      Filter = require('models/filter');
      
  return Backbone.Collection.extend({
    model: Filter,
    initialize: function() {
      
    },
    indexOfTimeSerie: function(timeSerie){
      for(var i=0;i<this.models.length;i++){
        if(timeSerie == this.models[i].timeSerie){
          return i;
        }
      }
      return -1;
    },
    push: function(timeSerie,filter){
      if(timeSerie == undefined){
        return;
      }
      var index = this.indexOfTimeSerie(timeSerie);
      if(index == -1){
        this.add(new Filter(timeSerie,filter));
      }else{
        this.models[index].addFilter(filter);
      }
    },
    getAllFilters: function(){
      var filters = [];
      for(var i = 0;i<this.models.length;i++){
        for(var j = 0;j<this.models.length;j++){
          filters.push({
            timeSerie:this.models[i].timeSerie.get('sr_id'),
            filter: this.models[i].name[j]
          });
        }
      }

      return filters;
    }
  });
});
