define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/volcano_select.html'),
      loading = require('text!templates/loading.html'),
      offline_dialog = require('text!templates/make_it_offline.html'),
      word_distance = require('helper/word-distance'),
      Handlebars = require('handlebars'),
      materialize = require('material');

  return Backbone.View.extend({
    el: '',
    className : 'volcano-select',
    template: _.template(template),
    loading: _.template(loading),
    offline_dialog : _.template(offline_dialog),
    events: {
      // 'change select': 'onSelectChange',
      'input input' : 'onChange',
      'click input' : 'onChange',
      'blur input' : 'onBlurSearchBox',
      'click a' : 'makeItOffline'
    },
    
    initialize: function(options) {
      _(this).bindAll('render');
      // this.showLoading();
      this.observer = options.observer;
      this.selectingVolcano = options.selectingVolcano;
      this.collection = options.collection;
      this.offline = options.offline;
      this.collection.fetch();  
      
      
      //console.log(this.collection);
      this.listenTo(this.collection, 'sync', this.render);
      this.selecting_vd_num = options.selecting_vd_num;
      
    },
    showLoading: function(){
      this.$el.html(this.loading);
    },
    render: function() {
      this.$el.html("");
      var temp = Handlebars.compile(template);
      // this.$el.html(template);
      /** selecting volcano from url **/
      var options ={no_volcano : true, offline: this.offline};
      if(this.selecting_vd_num != undefined ){
        for(var i=0;i<this.collection.models.length;i++){
          var model = this.collection.models[i];
          if(this.selecting_vd_num == model.get("vd_num")){
            this.selectingVolcano.attributes = model.attributes;
             // this.selectingVolcano.set('vd_id', model.id);
            this.selectingVolcano.trigger("update");
            options.no_volcano = false;
            options.vd_name = model.get("vd_name");
            var a = $('.search');
            
            break;
          }
        }  
      }

      var html = temp(options);
      this.$el.html(html);
      $('.volcanoes_select').material_select();
      $('.search-sugesstion').dropdown();
      // $('.modal-trigger').leanModal();
      
    },
    makeItOffline: function(e){
      if(e.currentTarget.id == 'offline-button'){
        this.trigger('make-offline');
      }
    },
    onBlurSearchBox: function(e){
      if(e.target.value != ''){
        e.target.value = "";
      }
    },
    // input search handler
    onChange: function(e){
      // var volcano = this.searchVolcanoes(e.target.value);
      // var listVolcanoTemplate = _.template();
      // $('.search').attr("placeholder",model.get("vd_name"));
      var temp = Handlebars.compile("{{#list volcanoes}}<li><a href=\"#vnum={{vd_num}}\">{{vd_name}}</a></li><li class=\"divider\"></li>{{/list}}");
      Handlebars.registerHelper('list', function(items, options) {
        var ret = "";
        for(var i=0, j=items.length; i<j; i++) {
          ret = ret+options.fn(items[i]);
        }
        return ret;
      });
      var volcanoes = this.searchVolcanoes(e.target.value);
      var options = {
        volcanoes: this.generateVolcanoes(volcanoes)
      };
      $('#dropdown1').html(temp(options));
      var searchSuggestion = $('.search-sugesstion');
      //show/hide suggestion part
      if(!searchSuggestion.hasClass('active')){
        $('.search-sugesstion').click(); 
        $('#dropdown1').val("");

      }
    },
    
    searchVolcanoes: function (input){
      if(input == ""){
        return this.collection.models;
      }
      // console.log(input);
      var output = [];
      for(var i = 0 ;i < this.collection.models.length ; i++){
        var volcano = this.collection.models[i];
        // console.log(volcano)
        if(word_distance.hammingDistance (input,volcano.get("vd_name"))<3){
          output.push(volcano);
        }else{
          if(word_distance.Levenshtein (input,volcano.get("vd_name"))<3){
            output.push(volcano);
          }
        }
        
      }
      
      return output;
    },
    generateVolcanoes: function(volcanoes){
      var output = [];
      for(var i = 0 ;i < volcanoes.length ; i++){
        output.push({
          vd_num: volcanoes[i].get("vd_num"),
          vd_name: volcanoes[i].get("vd_name")
        })
      }
      return output;
    },
    changeSelection: function(vd_id) {
      
      // $(document).ready(function() {
      //   $('select').val(vd_id);
      //   $('select').material_select();
      // });
      
    }

    // onSelectChange: function() {
    //   var vd_id = this.$el.find('select').val();
    //   for(var i=0;i<this.collection.models.length;i++){
    //     var model = this.collection.models[i];
    //     if(vd_id == model.id){
    //       this.selectingVolcano.set('vd_id',model.id);
    //       this.selectingVolcano.trigger("update");
    //       var state = {
    //         id:vd_id,
    //         a: model
    //       }
    //       Backbone.history.navigate("vnum="+model.get('vd_num'))

    //     }
    //   }
    // }
  });
});