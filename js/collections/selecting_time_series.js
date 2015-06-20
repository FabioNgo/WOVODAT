define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');

  return Backbone.Collection.extend({
    model: Serie,
    initialize: function() {      
    },

    // add: function(sr_id){
      
    // }
    onAdd: function(e) {
      e.fetch({
        success: function(collection, response) {
          // console.log(e);
          // console.log(response);
        }
      });
    },
    onRemove: function(e){
      e.fetch();
    }
  });
});