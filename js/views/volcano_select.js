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
      // read volcano parameter on url
      var  vd_num = this.getUrlParameter("vnum");
      if(vd_num == undefined){
        return;
      }
      for(var i=0;i<this.collection.models.length;i++){
        var model = this.collection.models[i];
        if(vd_num == model.get("vd_num")){
          this.selectingVolcano.set('vd_id', model.id); // .set auto call event in eventhandler 
          this.selectingVolcano.trigger("update");
          return;
        }
      }
      
    },

    changeSelection: function(vd_id) {
      
      this.$el.find('select').val(vd_id);
      
    },
    getUrlParameter: function(sParam) {
      var sPageURL = decodeURIComponent(window.location.search.substring(1)),
          sURLVariables = sPageURL.split('&'),
          sParameterName,
          i;

      for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
              return sParameterName[1] === undefined ? true : sParameterName[1];
          }
      }
    },
    onSelectChange: function() {
      var vd_id = this.$el.find('select').val();
      for(var i=0;i<this.collection.models.length;i++){
        var model = this.collection.models[i];
        if(vd_id == model.id){
          window.location.replace("http://localhost/eruption/?vnum="+model.get("vd_num"));
        }
      }
      // if (vd_id) {
      //   this.selectingVolcano.set('vd_id', vd_id); // .set auto call event in eventhandler 
      //   this.selectingVolcano.trigger("update");
      // }
    }
  });
});