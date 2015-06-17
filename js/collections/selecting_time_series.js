define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');

  return Backbone.Collection.extend({
    model: Serie,
    initialize: function() {      
    },

    onAdd: function(e) {
      e.fetch();
    },
    onRemove: function(e){
      e.fetch();
    }
  });
});