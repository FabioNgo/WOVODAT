define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/volcano_select.html'),
      loading = require('text!templates/loading.html'),
      Handlebars = require('handlebars'),
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
      this.selecting_vd_num = options.selecting_vd_num;
      
    },
    showLoading: function(){
      this.$el.html(this.loading);
    },
    render: function() {
      this.$el.html("");
      var temp = Handlebars.compile(template);
      Handlebars.registerHelper('list', function(items, options) {
        var ret = "";
        for(var i=0, j=items.length; i<j; i++) {
          ret = ret+options.fn(items[i]);
        }
        return ret;
      });

      var options = {
        volcanoes: this.generateVolcanoes(this.collection.models),
        selected: false,
      }
      
      /** selecting volcano from url **/
      if(this.selecting_vd_num != undefined ){
        for(var i=0;i<this.collection.models.length;i++){
          var model = this.collection.models[i];
          if(this.selecting_vd_num == model.get("vd_num")){
            this.selectingVolcano.set('vd_id', model.id); // .set auto call event in eventhandler 
            this.selectingVolcano.trigger("update");
            options.selected = true;
            break;
          }
        }  
      }

      var html = temp(options);
      this.$el.html(html);
      $('.volcanoes_select').material_select();

      
      
    },
    generateVolcanoes: function(volcanoes){
      var output = [];
      for(var i = 0 ;i < volcanoes.length ; i++){
        var select = false;
        if(volcanoes[i].get("vd_num") == this.selecting_vd_num){
          select = true;
        }
        output.push({
          vd_id: volcanoes[i].get("vd_id"),
          vd_name: volcanoes[i].get("vd_name"),
          select: select
        })

      }
      return output;
    },
    changeSelection: function(vd_id) {
      
      // $(document).ready(function() {
      //   $('select').val(vd_id);
      //   $('select').material_select();
      // });
      
    },
    
    onSelectChange: function() {
      var vd_id = this.$el.find('select').val();
      for(var i=0;i<this.collection.models.length;i++){
        var model = this.collection.models[i];
        if(vd_id == model.id){
          this.selectingVolcano.set('vd_id',model.id);
          this.selectingVolcano.trigger("update");
          var state = {
            id:vd_id,
            a: model
          }
          Backbone.history.navigate("vnum="+model.get('vd_num'))

        }
      }
    }
  });
});