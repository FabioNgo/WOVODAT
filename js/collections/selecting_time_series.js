define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');

  return Backbone.Collection.extend({
    model: Serie,
    initialize: function() {
      _(this).bindAll('onAdd');
      this.listenTo(this, 'add', this.onAdd);
    },

    onAdd: function(e) {
      e.fetch();
    }
  });
});