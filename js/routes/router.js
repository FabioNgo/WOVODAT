define(function(require) {
  'use strict';
  var Backbone = require('backbone'),
      Page = require('views/page');

  return Backbone.Router.extend({
    initialize: function(options) {
      Backbone.history.start();
      this.vnum = -1;
      this.ed_stime = -1;
      this.ed_etime = -1;
    },

    routes: {
      '*anything': 'loadPage',
      
    },
    loadPage: function(queryString){
      var options = {};
      if(queryString!=null){
        var paramsDivider = '&';
        var keyValDivider = '=';
        var options ={};
        var params=queryString.split(paramsDivider);
        
        for(var i = 0 ;i< params.length;i++){
          var keyVal = params[i].split(keyValDivider);
          
          options[keyVal[0]] = keyVal[1];
          
        }
      }
      
      new Page(options);
    },
    // timeSeriesAnalyze: function(timeSeriesStrings){
    //   timeSeriesStrings = timeSeriesStrings.replace("%2F","/");
    //   timeSeriesStrings = timeSeriesStrings.replace("%20"," ");
    //   var filterDivider = '|';
    //   var infoDivider = '.';
    //   var timeSeriesStrings = timeSeriesStrings.split(filterDivider);
    //   var timeSeries = [];
    //   for(var i = 0;i<timeSeriesStrings.length;i++){
    //     var string = timeSeriesStrings[i];
    //     var values = string.split(infoDivider);
    //     var value = {
    //
    //       category : values[0],
    //       component:values[1],
    //       data_type: values[2],
    //       sta_id1 : values[3],
    //       sta_id2 : values[4]
    //
    //     }
    //     timeSeries.push(value);
    //   }
    //   return timeSeries;
    // }
  });
});