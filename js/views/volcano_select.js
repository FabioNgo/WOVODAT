define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/volcano_select.html'),
      loading = require('text!templates/loading.html'),
      materialize = require('material');

  return Backbone.View.extend({
    el: '',

    template: _.template(template),
    loading: _.template(loading),
    events: {
      'change select': 'onSelectChange'
    },
    
    initialize: function(options) {
      _(this).bindAll('render');
      // this.showLoading();
      this.observer = options.observer;
      this.selectingVolcano = options.selectingVolcano;
      this.collection = options.collection;
      this.collection.fetch();
      this.listenTo(this.collection, 'sync', this.render);
      
    },
    showLoading: function(){
      this.$el.html(this.loading);
    },
    render: function() {
      this.$el.html(this.template({
        volcanoes: this.collection.models
      }));

      // read volcano parameter on url
      var  vd_num = parseInt(this.getUrlParameter("vnum"));
      if(isNaN(vd_num)){
        $('select').material_select();
      }else{
        for(var i=0;i<this.collection.models.length;i++){
          var model = this.collection.models[i];
          if(vd_num == model.get("vd_num")){
            this.selectingVolcano.set('vd_id', model.id); // .set auto call event in eventhandler 
            this.selectingVolcano.trigger("update");
            break;
          }
        }  
      }
      
      
    },

    changeSelection: function(vd_id) {
      
      $(document).ready(function() {
        $('select').val(vd_id);
        $('select').material_select();
      });
      
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
          window.location.replace("index.php?vnum="+model.get("vd_num"));
        }
      }
      // if (vd_id) {
      //   this.selectingVolcano.set('vd_id', vd_id); // .set auto call event in eventhandler 
      //   this.selectingVolcano.trigger("update");
      // }
    }
  });
});