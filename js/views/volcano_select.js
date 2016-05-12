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
    className : 'volcano-select',
    template: _.template(template),
    loading: _.template(loading),
    events: {
      'change select': 'onSelectChange',
      'input input' : 'onChange',
      'click input' : 'onChange'
    },
    
    initialize: function(options) {
      _(this).bindAll('render');
      // this.showLoading();
      this.observer = options.observer;
      this.selectingVolcano = options.selectingVolcano;
      this.collection = options.collection;
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
      var options ={};
      if(this.selecting_vd_num != undefined ){
        for(var i=0;i<this.collection.models.length;i++){
          var model = this.collection.models[i];
          if(this.selecting_vd_num == model.get("vd_num")){
            this.selectingVolcano.set('vd_id', model.id);
             // this.selectingVolcano.set('vd_id', model.id);
            this.selectingVolcano.trigger("update");
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
      
      
    },
    // onClickInput: function(e){
    //   if(!searchSuggestion.hasClass('active')){
    //     $('.search-sugesstion').click();  
    //   }

    // }
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
          }
          $('#dropdown1').html(temp(options))
          var searchSuggestion = $('.search-sugesstion');
          //show/hide suggestion part
          if(!searchSuggestion.hasClass('active')){
            $('.search-sugesstion').click();  
          }
          // if(e.target.value != ""){
          //   if(!searchSuggestion.hasClass('active')){
          //     $('.search-sugesstion').click();  
          //   }
          // }else{
          //   if(searchSuggestion.hasClass('active')){
          //     $('.search-sugesstion').click();  
          //   }
          // }
          
          // console.log($('.search-sugesstion'));
    },
    Levenshtein : function( str_m, str_n ) { 
      var previous, current, matrix
    // Constructor
      matrix = this._matrix = []

    // Sanity checks
      if ( str_m == str_n )
        return this.distance = 0
      else if ( str_m == '' )
        return this.distance = str_n.length
      else if ( str_n == '' )
        return this.distance = str_m.length
      else {
        // Danger Will Robinson
        previous = [ 0 ]
        _.forEach( str_m, function( v, i ) { i++, previous[ i ] = i } )

        matrix[0] = previous
        _.forEach( str_n, function( n_val, n_idx ) {
          current = [ ++n_idx ]
          _.forEach( str_m, function( m_val, m_idx ) {
            m_idx++
            if ( str_m.charAt( m_idx - 1 ) == str_n.charAt( n_idx - 1 ) )
              current[ m_idx ] = previous[ m_idx - 1 ]
            else
              current[ m_idx ] = Math.min
                ( previous[ m_idx ]     + 1   // Deletion
                , current[  m_idx - 1 ] + 1   // Insertion
                , previous[ m_idx - 1 ] + 1   // Subtraction
                )
          })
          previous = current
          matrix[ matrix.length ] = previous
        })

        return this.distance = current[ current.length - 1 ]
      }
    },
    hammingDistance: function(str1,str2) {
      var dist = 0;
      // console.log(str1);
      str1 = str1.toLowerCase();
      str2 = str2.toLowerCase();

       for(var i = 0; i < str1.length; i++) {

          if(str2[i] && str2[i] !== str1[i]) {
              dist += Math.abs(str1.charCodeAt(i) - str2.charCodeAt(i)) + Math.abs(str2.indexOf( str1[i] )) * 2;
          } 
          else if(!str2[i]) {
              //  If there's no letter in the comparing string
              dist += dist;
          }
      }
      return dist;
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
        if(this.hammingDistance (input,volcano.get("vd_name"))<3){
          output.push(volcano);
        }else{
          if(this.Levenshtein (input,volcano.get("vd_name"))<3){
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
          vd_name: volcanoes[i].get("vd_name"),
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