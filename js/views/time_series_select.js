define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/time_series_select.html');

  return Backbone.View.extend({
    el: '',

    events: {
      'change input': 'onChange'
    },

    template: _.template(template),
    
    initialize: function(options) {
      _(this).bindAll('render', 'changeVolcano');
      this.volcano = options.volcano;
      this.selectings = options.selectingTimeSeries;
      this.observer = options.observer;
      this.listenTo(this.volcano, 'change', this.changeVolcano);
      this.listenTo(this.collection, 'sync', this.render);
    },

    changeVolcano: function() {
      this.collection.changeVolcano(this.volcano.get('vd_id'));
      this.selectings.reset();
    },

    render: function() {
      this.$el.html(this.template({
        timeSeries: this.collection.models
      }));
    },

    onChange: function(event) {
      var input = event.target,
          id = $(input).val();
      if ($(input).is(':checked'))
        this.selectings.add(id);
      else 
        this.selectings.remove(this.selectings.get(id));
    },

    destroy: function() {
      // From StackOverflow with love.
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});