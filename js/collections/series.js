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
      this.fetch({
        success: function(collection,response){
          collection.groupedData = {};
          var currentCategory = "";
            //success: function(collection,response){
          for(var i=0;i<response.length;i++){
            var model = response[i];
            if(currentCategory == "" | currentCategory != model.category){
              collection.groupedData[model.category] = [];
              currentCategory = model.category;
            }
            collection.groupedData[currentCategory].push(model);
          }
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
            // console.log(e); 
                var filters = [];
                
                var data = model.get('data').data;
                // console.log(data);
                if(data == undefined){
                  return;
                }
                for (var i = 0; i < data.length; i++) {
                  var index = -1;
                  /** find index of filter in filters**/
                  for(var j = 0;j<filters.length;j++){
                    // console.log({0:filters[j].name,1: data[i].filter});
                    if(filters[j].name == data[i].filter){
                      index = j;
                      break;
                    }
                  }
                }
                model.filters = filters;
                // console.log(model);
                
                model.loaded = true;
                model.name = model.getName();
              }
            })
          }  
          return this.models[i];           
 
          
        }
      }
    },
  });
});