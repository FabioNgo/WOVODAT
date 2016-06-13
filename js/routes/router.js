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
      "vnum=*:number" : 'loadPage',
      "vnum=*:vnum/ed_stime=*:edstimeNum/ed_etime=*:edetimeNum" : 'loadPage2',
      '*anything': 'loadPage1',
      
    },
    loadPage: function(number){
      var selecting_vd_num = parseInt(number);
      var a = new Page(selecting_vd_num,undefined,undefined);
    },
    loadPage1: function(){
      var a = new Page();
    },
    loadPage2: function(vnum,edstimeNum,edetimeNum){
      var selecting_vd_num = parseInt(vnum);
      var ed_stime_num = parseFloat(edstimeNum);
      var ed_etime_num = parseFloat(edetimeNum);
      var a = new Page(selecting_vd_num,ed_stime_num,ed_etime_num);
    }
  });
});