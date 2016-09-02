define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),

      Filter = require('models/filter');
      
  return Backbone.Collection.extend({
    model: Filter,
    initialize: function() {
      this.empty = true;
    },
    indexOfTimeSerie: function(timeSerie){
      var items = this[timeSerie.get("category")];
      for(var i=0;i<items.length;i++){
        if(timeSerie == items[i].timeSerie){
          return i;
        }
      }
      return -1;
    },
    push: function(timeSerie,filter){

      if(timeSerie == undefined){
        return;
      }
      var category = timeSerie.get("category");
      if(this[category]==undefined){
        this[category] = [];
      }
      var index = this.indexOfTimeSerie(timeSerie);
      if(index == -1){
        this[category].push(new Filter(timeSerie,filter));
      }else{
        this[category][index].addFilter(filter);
      }
      this.length++;
    },
    removeFilter:function(filter){
      if(this.length <=0){
          return;
      }
      var groupedFilters = this[filter.timeSerie.get('category')];
      groupedFilters = _.filter(groupedFilters, function(groupedFilter){
        groupedFilter.name = _.filter(groupedFilter.name, function(name){
          return name == filter.name;
        });
        return groupedFilter.name!=0;
      });
      if(groupedFilters.length == 0){
        delete this[filter.timeSerie.get('category')]
      }else{
       this[filter.timeSerie.get('category')] = groupedFilters;
      }
      this.length--;
      
    },
    getAllFilters: function(category){
      var filters = [];
      if(this[category]!= undefined){
        for(var i = 0;i<this[category].length;i++){
          for(var j = 0;j<this[category][i].filterAttributes.length;j++){
            filters.push({
              timeSerie:this[category][i].timeSerie,
              filterAttributes: [this[category][i].filterAttributes[j]]
            });
          }
        }
      }
      return filters;
    }
    // getSeparatedFilters : function(category){

    // }
    
  });
});
