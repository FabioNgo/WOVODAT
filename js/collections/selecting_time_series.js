define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      Serie = require('models/serie');

  return Backbone.Collection.extend({
    model: Serie,
    initialize: function() {
      _(this).bindAll('onAdd','onRemove');
      this.listenTo(this, 'add', this.onAdd);
      this.listenTo(this,'remove', this.onRemove);
    },

    onAdd: function(e) {
      e.fetch();
    },
    onRemove: function(e){
      e.fetch();
    }
  });
});