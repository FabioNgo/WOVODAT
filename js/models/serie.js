define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    

    initialize: function(options) {
      this.sr_id = options.sr_id;
      this.url = 'api/?data=time_serie&sr_id=' + options.sr_id;
    },
    getName: function(){
      return ""+this.get('category')+" - " + this.get('station_code')+" (" + this.get('component') +")";
    },
    /** return the data of time serie in term of filter**/
    getDataFromFilter: function(filterName){
      var data = [];
      var filters = this.filters;
      if(filters == undefined){
        return undefined;
      }
      var filter;
      //find filter
      for(var i =0;i<filters.length;i++){
        if(filters[i].name == filterName){
          filter = filters[i];
          break;
        }
      }
      // get data
      for(var i=0; i< filter.dataIndex.length;i++ ){
        data.push(this.get('data')[filter.dataIndex[i]]);
      }
      return data;
    }

  });
});