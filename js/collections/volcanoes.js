define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Volcano = require('models/volcano');

  return Backbone.Collection.extend({
    model: Volcano,
    url: 'api/?data=volcano_list'
  });
});
