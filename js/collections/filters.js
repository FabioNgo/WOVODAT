define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),

      Filter = require('models/filter');
      
  return Backbone.Collection.extend({
    model: Filter,
    initialize: function() {
      _(this).bindAll('OptionForFilterEQType');
      console.log(this.data);
      if (this.data.get('category').indexOf('Seismic') == -1) {
        if(this.data.get('data_type').indexOf("Interval")!=-1 || this.data.get('data_type').indexOf("EVS")!=-1) {
          console.log(OptionForFilterEQType());
        }
      }
    },
    OptionForFilterEQType: function() {
      var data = this.data.get('data');
      var list=[];
      var must=["V","VT","LF","VLP","H","RF","R"];
      for (var i in must) {
        var ok=false;
        for (var j in data) {
          console.log(data[j]);
        }
      }
      
    }
  });
});
