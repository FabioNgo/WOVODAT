define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      filterColor = require('models/filter_color');
  //1
  return Backbone.Collection.extend({
    model: filterColor,
    url: 'api/?data=filter_color_list&offline=false',
    initialize: function(options){
      if(options.offline){
        this.url = 'offline-data/filter_color_list.json';
      }
    }
    });
});

