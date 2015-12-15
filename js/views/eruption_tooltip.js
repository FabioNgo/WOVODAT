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
      this.$el.addClass('tooltip');
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

    update: function(pos, item) {
      if (item) {
        if(this.previous.dataType === item.series.dataType){
          if (this.previous.dataIndex === item.dataIndex) {
            this.move(pos.pageX, pos.pageY);
          } else {
            this.previous.dataIndex = item.dataIndex;
            this.html = this.template({
              name: item.series.name,
              startTime: DateHelper.formatDate(item.series.startTime),
              endTime: DateHelper.formatDate(item.series.endTime),
              value: item.series.data[0][1]
            })
            this.render(pos.pageX, pos.pageY, this.html);
          }
        }else{
          this.previous.dataIndex = item.dataIndex;
          this.previous.dataType = item.series.dataType;
            this.html = this.template({
              name:item.series.name,
              startTime: DateHelper.formatDate(item.series.startTime),
              endTime: DateHelper.formatDate(item.series.endTime),
              value: item.series.data[0][2]
            })
            this.render(pos.pageX, pos.pageY, this.html);
        }
      } else {
        this.hide();
      }
    
    }
  });
});