define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      DateHelper = require('helper/date');
      

  return Backbone.View.extend({
    el: '',

    initialize: function(options) {
      this.template = _.template(options.template);
      _(this).bindAll('remove');
      this.$el.html('<div></div>');
      this.$el.addClass("graph-tooltip");
      this.hide();
      this.$el.appendTo('body');
    },

    move: function(x, y) {
      this.$el.css({
        top: y + 5,
        left: x + 20,
      });
      this.show();
    },

    show: function() {
      this.$el.show();      
    },

    hide: function() {
      this.$el.hide();
    },

    render: function(x, y, content) {
      this.$el.html(content);
      this.move(x, y);
    },

    previous: {
      dataIndex: -8121993,
      dataType: undefined
    },

    update: function(pos, item,name) {
      if (item) {
        this.html = this.template({
              name:name,
              startTime: DateHelper.formatDate(item.datapoint[0]),
              endTime: DateHelper.formatDate(item.datapoint[1]),
              value: item.datapoint[3]
            })
            this.render(pos.pageX, pos.pageY, this.html);
      } else {
        this.hide();
      }
    
    }
  });
});