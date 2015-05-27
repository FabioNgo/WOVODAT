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
      _(this).bindAll('render', 'changeSelection');
      this.observer = options.observer;
      this.selectingVolcano = options.selectingVolcano;

      this.collection.fetch();
      this.listenTo(this.collection, 'sync', this.render);
      this.listenTo(this.selectingVolcano, 'change', this.changeSelection);
    },

    render: function() {
      this.$el.html(this.template({
        volcanoes: this.collection.models
      }));
    },

    changeSelection: function(e) {
      this.$el.find('select').val(this.selectingVolcano.get('vd_id'));
    },

    onSelectChange: function() {
      var vd_id = this.$el.find('select').val();
      if (vd_id) 
        this.selectingVolcano.set('vd_id', vd_id);
    }
  });
});