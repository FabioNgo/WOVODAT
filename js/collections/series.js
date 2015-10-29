define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');

  return Backbone.Collection.extend({
    model: Serie,
    initialize: function() {
      // this.isVocalnoChanged = false;
    },
    changeVolcano: function(vd_id, handler) {
      this.url = 'api/?data=time_series_list&vd_id=' + vd_id;
      this.fetch({
        // success: function(collection,response){

        //   for(var i=0; i<collection.length; i++) {
        //     collection.models[i].fetch({
        //       success: function(model, response) {
        //       // console.log(e);
        //         var filters = [];
                
        //         var data = model.get('data');
        //         // console.log(data);
        //         if(data == undefined){
        //           return;
        //         }
        //         for (var i = 0; i < data.length; i++) {
        //           var index = -1;
        //           /** find index of filter in filters**/
        //           for(var j = 0;j<filters.length;j++){
        //             // console.log({0:filters[j].name,1: data[i].filter});
        //             if(filters[j].name == data[i].filter){
        //               index = j;
        //               break;
        //             }
        //           }
        //           // var index = this.indexOfFilter(filters,data[i].filter);
        //           /** push data **/
        //           if(index == -1){
        //             filters.push({name: data[i].filter, dataIndex: []});
        //             index = filters.length-1;
        //           }
        //           filters[index].dataIndex.push(i);
        //         };
        //         model.filters = filters;
        //         // console.log(model);
        //       }
        //     })
        //   }
        // }
      });
      
    },

    updateData: function(models){
        //success: function(collection,response){
            models.fetch({
                success: function(model, response) {
              // console.log(e);
                var filters = [];
                
                var data = model.get('data');
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
                  // var index = this.indexOfFilter(filters,data[i].filter);
                  /** push data **/
                  if(index == -1){
                    filters.push({name: data[i].filter, dataIndex: []});
                    index = filters.length-1;
                  }
                  filters[index].dataIndex.push(i);
                };
                model.filters = filters;
                // console.log(model);
              }
            })


          // for(var i=0; i<collection.length; i++) {
          //   collection.models[i].fetch({
          //     success: function(model, response) {
          //     // console.log(e);
          //       var filters = [];
                
          //       var data = model.get('data');
          //       // console.log(data);
          //       if(data == undefined){
          //         return;
          //       }
          //       for (var i = 0; i < data.length; i++) {
          //         var index = -1;
          //         /** find index of filter in filters**/
          //         for(var j = 0;j<filters.length;j++){
          //           // console.log({0:filters[j].name,1: data[i].filter});
          //           if(filters[j].name == data[i].filter){
          //             index = j;
          //             break;
          //           }
          //         }
          //         // var index = this.indexOfFilter(filters,data[i].filter);
          //         /** push data **/
          //         if(index == -1){
          //           filters.push({name: data[i].filter, dataIndex: []});
          //           index = filters.length-1;
          //         }
          //         filters[index].dataIndex.push(i);
          //       };
          //       model.filters = filters;
          //       // console.log(model);
          //     }
          //   })
          //}
    },

    getTimeSerie: function(sr_id){
      for(var i =0;i<this.models.length;i++){
        if(this.models[i].sr_id == sr_id){
          
            
          return this.models[i];           
 
          
        }
      }
      // var that = this;
      // timeSerie.fetch();
    },
    // addTimeSerie: function(timeSerie){
    //   this.add(timeSerie);
    //   var x=0;
    // },
    // addModel: function(id){
    //   var timeSerie = new Serie(id);
      
    //   this.getData(timeSerie);
    //   console.log(this);
    // },
    // // add: function(sr_id){
      
    // // }
    
    
    
    // getTimeSerie: function(sr_id){
    //   for(var i=0;i<this.models.length;i++){
    //     if(this.models[i].get('sr_id') == sr_id){
    //       return this.models[i];
    //     }
    //   }
    //   return undefined;
    // }
  });
});