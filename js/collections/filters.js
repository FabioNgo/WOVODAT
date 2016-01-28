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
      
    },
    removeFilter:function(filter){
      var groupedFilters = this[filter.timeSerie.get('category')];
      groupedFilters = _.filter(groupedFilters, function(groupedFilter){
        groupedFilter.name = _.filter(groupedFilter.name, function(name){
          return name == filter.name;
        })
        return groupedFilter.name!=0;
      })
      if(groupedFilters.length == 0){
        delete this[filter.timeSerie.get('category')]
      }else{
       this[filter.timeSerie.get('category')] = groupedFilters;
      }
    },
    getAllFilters: function(category){
      var filters = [];
      if(this[category]!= undefined){
        for(var i = 0;i<this[category].length;i++){
          for(var j = 0;j<this[category][i].filterAttributes.length;j++){
            filters.push({
              timeSerie:this[category][i].timeSerie.get('sr_id'),
              filter: this[category][i].filterAttributes[j].name
            });
          }
        }
      }
      return filters;
    },
    
    
  });
});
