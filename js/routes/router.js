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
     

      "vnum=*:vnum&ed_stime=*:edstimeNum&ed_etime=*:edetimeNum" : 'loadPage2',
      "vnum=*:vnum&timeSeries=*:timeSeriesStrings" : 'loadPage3',
       "vnum=*:number" : 'loadPage',
      '*anything': 'loadPage1',
      
    },
    loadPage: function(number){
      var selecting_vd_num = parseInt(number);
      var a = new Page(selecting_vd_num,undefined,undefined,[]);
    },
    loadPage1: function(e){
      var b =this;
      var a = new Page();
    },
    loadPage3: function(vnum,timeSeriesStrings){
      timeSeriesStrings = timeSeriesStrings.replace("%2F","/");
      timeSeriesStrings = timeSeriesStrings.replace("%20"," ");
      var filterDivider = '|';
      var infoDivider = '.';
      var timeSeriesStrings = timeSeriesStrings.split(filterDivider);
      var timeSeries = [];
      for(var i = 0;i<timeSeriesStrings.length;i++){
        var string = timeSeriesStrings[i];
        var values = string.split(infoDivider);
        var value = {

          category : values[0],
          component:values[1],
          data_type: values[2],
          sta_id1 : values[3],
          sta_id2 : values[4]
          
        }
        timeSeries.push(value);
      }
      var a = new Page(vnum,undefined,undefined,timeSeries);
    },
    loadPage2: function(vnum,edstimeNum,edetimeNum){
      var selecting_vd_num = parseInt(vnum);
      var ed_stime_num = parseFloat(edstimeNum);
      var ed_etime_num = parseFloat(edetimeNum);
      var a = new Page(selecting_vd_num,ed_stime_num,ed_etime_num,undefined);
    }
  });
});