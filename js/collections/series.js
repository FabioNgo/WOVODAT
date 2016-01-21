define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');
  //1
  return Backbone.Collection.extend({
    model: Serie,
    

    initialize: function() {
      // this.isVocalnoChanged = false;
    },
    
    changeVolcano: function(vd_id, handler) {

      this.url = 'api/?data=time_series_list&vd_id=' + vd_id;
      var categories=["Seismic","Deformation","Gas","Hydrology","Thermal","Field","Meteology"];
      for(var i = 0; i<categories.length;i++){
        delete this[categories[i]];
      }
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
          }
          // console.log(collection);
          collection.trigger("loaded");
        }
      });
      
    },

    updateData: function(){
        //success: function(collection,response){
        for(var i=0;i<this.models.length;i++){
          
        }
    },

    get: function(sr_id){
      for(var i =0;i<this.models.length;i++){
        if(this.models[i].sr_id == sr_id){
          
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
    },
  });
});