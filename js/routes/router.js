define(function(require) {
  'use strict';
  var Backbone = require('backbone'),
      Page = require('views/page');

  return Backbone.Router.extend({
    initialize: function(options) {
      Backbone.history.start();
      this.vnum = -1;
    },

    routes: {
      '': 'loadPage',
      'vnum=:number' : 'loadPage'
    },
    loadPage: function(number){
      var selecting_vd_num = parseInt(number);
      var a = new Page(selecting_vd_num);
    },
  });
});