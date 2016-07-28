define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');
  //1
  return Backbone.Collection.extend({
    model: Serie,
    

    initialize: function(options) {
      // this.isVocalnoChanged = false;
      this.offline = options.offline;
    },
    
    changeVolcano: function(vd_id, handler) {
      if(this.offline){
        this.url = 'offline-data/serie-list.json';
      }else{
        this.url = 'api/?data=time_series_list&vd_id=' + vd_id;
      }
      
      var categories=["Seismic","Deformation","Gas","Hydrology","Thermal","Field","Meteology"];
      for(var i = 0; i<categories.length;i++){
        delete this[categories[i]];
      }
      this.length = 0;
      this.fetch({
        success: function(collection,response){
          //group Data in categroy
          
          var currentCategory = "";
            //success: function(collection,response){
          for(var i=0;i<response.length;i++){
            var model = collection.models[i];
            var item = model.attributes;
            var station1 = "";
            var station2 = "";
            if(item.station_id1 == item.station_id2){
              station1 = item.station_code1;
              station2 = "";
            }else{
              if(item.station_id1 == "0"){
                station1 = "";
                station2 = item.station_code2;
              }else {
                station1 = item.station_code1;
                if(item.station_id == "0"){
                  station2 = "";
                }else{
                  station2 = " - "+item.station_code2;
                }
              }
            }
            model.attributes.showingName = station1 + station2 + "(" + item.component + ")";
            if(currentCategory == "" | currentCategory != item.category){
              collection[item.category] = [];
              currentCategory = item.category;
            }
            collection[currentCategory].push(model);
            collection.length++;
          }
          // console.log(collection);
          collection.trigger("loaded");
        }
      });
      
    },

    

    get: function(serie){
      for(var i =0;i<this.models.length;i++){
        if(serie.sr_id != undefined){
          if(this.models[i].get('sr_id') == serie.sr_id){
            
            if(!this.models[i].loaded){
              this.models[i].fetch({
                success: function(model, response) {

                  
                  model.loaded = true;
                  
                }
              })
            }  
            return this.models[i];           
   
            
          }
        }else{
          if(this.models[i].get('category') == serie.category
            && this.models[i].get('component') == serie.component
            && this.models[i].get('data_type') == serie.data_type
            && this.models[i].get('station_id2')== serie.sta_id1
            && this.models[i].get('station_id2') == serie.sta_id2
            ){
            
            if(!this.models[i].loaded){
              this.models[i].fetch({
                success: function(model, response) {

                  
                  model.loaded = true;
                  
                }
              })
            }  
            return this.models[i];           
   
            
          }
        }
      }
    },
  });
});