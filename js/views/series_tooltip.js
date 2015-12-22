define(function(require) {
  'use strict';
  var $ = require('jquery'),
      _ = require('underscore'),
      Backbone = require('backbone'),
      

      DateHelper = require('helper/date');

  return Backbone.View.extend({
    el: '',
    initialize: function(options) {
      this.template = _.template(options.template);
      _(this).bindAll('remove');
      this.$el.html("asdasdasd");
      // var a = $('.time-series-graph-container');
      
    },

    move: function(x, y) {
      // console.log(x,y);
      this.$el.css({
        top: y + 5,
        left: x + 20,
      });
      this.$el.show();
    },

    show: function() {
      this.$el.show();      
    },

    hide: function() {
      this.$el.hide();
    },

    render: function(x, y, content) {
      console.log(x,y);
      this.$el.html(content);
      this.move(x, y);
      this.$el.appendTo('body');
      this.$el.addClass("graph-tooltip");
    },

    previous: {
      dataIndex: -8121993
    },

    update: function(pos, item) {
      if (item) {
        if (this.previous.dataIndex === item.dataIndex) {
          this.move(pos.pageX, pos.pageY);
        } else {
          this.previous.dataIndex = item.dataIndex;
          this.html = this.template({
            time: DateHelper.formatDate(item.series.data[item.dataIndex][0]),
            value: item.series.data[item.dataIndex][1]
          })
          this.render(pos.pageX, pos.pageY, this.html);
        }
      } else {
        this.hide();
      }
    }
  });
});