define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/make_it_offline.html'),
      Handlebars = require('handlebars'),
      materialize = require('material');

  return Backbone.View.extend({
    el: '',
    className : 'volcano-select',
    template: _.template(template),
    events: {
    },
    
    initialize: function(options) {
      _(this).bindAll('render');
      this.selectingVolcano = options.selectingVolcano;
      
    },
    render: function() {
      var temp = Handlebars.compile(template);
      var options = {};
      this.$el.html(temp(options));
      
      $('#make-it-offline-dialog').openModal();
      
    },
    makeOffline : function(selectingVolcano){
      this.selectingVolcano = selectingVolcano;
      this.render();
      var self = this;
      $.getJSON("api/?data=volcano_list&offline=true");
      $.getJSON("api/?data=filter_color_list&offline=true");
      $.getJSON("api/?offline=true&data=eruption_list&vd_id=583");
      $.getJSON("api/?data=eruption_forecast_list&offline=true&vd_id=583")
      $.getJSON("api/?data=time_series_list&offline=true&vd_id=583",function(json){
        // console.log(json);
        self.getData(json);
      });

    },
    getData : function(time_series_list){
      
      for(var i=0;i<time_series_list.length;i++){
        var time_serie = time_series_list[i];
        var url = 'api';
        var data = {
          data: 'time_serie',
          serie: time_serie,
          offline: true
        }
        for (var property in time_serie) {
            if (time_serie.hasOwnProperty(property)) {
                // do stuff
                // url = url + "&serie[" + property + "]=" + time_serie[property];
            }
        }
        (function(i) { // protects i in an immediately called function
          $.getJSON(url,data,function(json){
            var percentage = Math.round((i+1)/time_series_list.length*100);
            // console.log(percentage);
            $('#progressbar').css('width',(percentage + '%'));
            $('#progress-detail').html(percentage + '%');
          });
        })(i);
      }
    }
  });
});