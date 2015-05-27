define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/eruption_select.html');

  return Backbone.View.extend({
    el: '',

    template: _.template(template),

    events: {
      'change select': 'onChangeEruption'
    },
    
    initialize: function(options) {
      _(this).bindAll('render', 'fetchEruptions', 'changeEruption');
      
      this.observer = options.observer;
      this.volcano = options.volcano;
      this.selectingEruption = options.selectingEruption;

      this.listenTo(this.volcano, 'change', this.fetchEruptions);
      this.listenTo(this.collection, 'sync', this.render);
      this.listenTo(this.selectingEruption, 'change', this.changeEruption);
    },

    fetchEruptions: function() {
      this.collection.changeVolcano(this.volcano.get('vd_id'));
    },

    changeEruption: function(e) {
      this.$el.find('select').val(this.selectingEruption.get('ed_id'));
      this.$el.find('select').change();
    },

    render: function() {
      this.$el.html(this.template({
        eruptions: this.collection.models
      }));
    },

    onChangeEruption: function() {
      var ed_id = this.$el.find('select').val(),
          startTime = this.collection.get(ed_id).get('ed_stime');

      this.selectingEruption.set('ed_id', ed_id);
      this.observer.trigger('change-start-time', startTime);
    }
  });
});