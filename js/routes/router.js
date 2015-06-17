define(function(require) {
  'use strict';
  var Backbone = require('backbone'),
      Page = require('views/page');

  return Backbone.Router.extend({
    initialize: function(options) {
      Backbone.history.start();
    },

    routes: {
      '': 'loadPage'
    },

    loadPage: function() {
       var a = new Page();
    }
  });
});