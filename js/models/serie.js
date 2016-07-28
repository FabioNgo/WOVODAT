define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    

    initialize: function(options) {
     
        // Your server goes below
        //options.url = 'http://localhost:8000' + options.url;
      // this.sr
      if(!options.offline){
        this.url = 'api/?offline=false&data=time_serie'; 
        for (var property in options) {
          if (options.hasOwnProperty(property)) {
              // do stuff
              this.url = this.url + "&serie[" + property + "]=" + options[property];
          }
        }
      }else{
        this.url = 'offline-data/'+options.sr_id+'.json';
      }
      
      
    	this.loaded = false;
      
    },
    getName: function(){
      var data = this.attributes;
      var station1 = "";
      var station2 = "";
      if(data.station_id1 == data.station_id2){
        station1 = data.station_code1;
        station2 = "";
      }else{
        if(data.station_id1 == "0"){
          station1 = "";
          station2 = data.station_code2;
        }else {
          station1 = data.station_code1;
          if(data.station_id == "0"){
            station2 = "";
          }else{
            station2 = " - "+data.station_code2;
          }
        }
      }
      var component = data.component;
      return station1 + station2 + "(" + component +")";
      
    },
    /** return the data of time serie in term of filter**/
    getDataFromFilter: function(filterName){
      var data = [];
      var serieDatas = this.get('data').data;
      for(var i=0; i< serieDatas.length;i++ ){
        var serieData = serieDatas[i];
        if(filterName == serieData.filter){
          data.push(serieData);
        }
        
      }
      return data;
    }

  });
});