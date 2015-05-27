define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Filter = require('models/filter');

  return Backbone.Collection.extend({
    model: Filter,
    initialize: function() {
    }
  });
});