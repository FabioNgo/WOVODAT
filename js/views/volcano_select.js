define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/volcano_select.html');

  return Backbone.View.extend({
    el: '',

    template: _.template(template),

    events: {
      'change select': 'onSelectChange'
    },
    
    initialize: function(options) {
      _(this).bindAll('render');
      this.observer = options.observer;
      this.selectingVolcano = options.selectingVolcano;
      this.collection = options.collection;
      this.collection.fetch();
      this.listenTo(this.collection, 'sync', this.render);
    },

    render: function() {
      this.$el.html(this.template({
        volcanoes: this.collection.models
      }));
    },

    changeSelection: function(vd_id) {
      this.$el.find('select').val(vd_id);
    },

    onSelectChange: function() {
      var vd_id = this.$el.find('select').val();
      if (vd_id) {
        this.selectingVolcano.set('vd_id', vd_id); // .set auto call event in eventhandler 
        this.selectingVolcano.trigger("update");
      }
    }
  });
});