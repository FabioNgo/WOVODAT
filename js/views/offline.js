define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      JSZip = require('jszip'),
      FileSaver = require('FileSaver'),
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
      this.volcano_list = options.volcanoes.models;
      this.eruption_list = options.eruptions.models;
      this.eruption_forecast_list = options.eruptionForecasts.models;
      this.time_series_list = options.timeSeries.models;
      this.filter_color_list = options.filterColorCollection.models;
      this.listFile = {};
      
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
      $.getJSON("api/?data=offline_files_list",function(json){
        self.listFile = json;
        self.makeZipFile();
      })


    },
    getFile : function(options){
      var deferredObject = $.Deferred();
      var xhr = new XMLHttpRequest();
      xhr.url = options.url;
      xhr.zip = options.zip;
      xhr.success = options.success;
      xhr.responseType = options.dataType;
      // xhr.pos = options.pos;
      xhr.target = options.target;
      xhr.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){
          //this.response is what you're looking for
          var data = this.response;
          this.success(data);
          var length = this.target.gettingFiles.length;
          this.target.pos++;
          // console.log(this.target.pos);

          var percentage = Math.round((this.target.pos)*100/length);
              // // console.log(percentage);
          $('#progressbar').css('width',(percentage + '%'));
          $('#progress-detail').html(percentage + '%');
          deferredObject.resolve("success");
        }
      }
      
      xhr.open('GET', xhr.url);
      xhr.send();  
      return deferredObject.promise();
    },
    makeZipFile: function (){
      // console.log(options);
      var zip = new JSZip();
      zip.file(this.selectingVolcano.get('vd_name')+'.bat','start miniweb.exe \nstart chrome http://localhost:8000/offline.html#vnum='+this.selectingVolcano.get('vd_num'));
      zip.file('htdocs/offline-data/volcano_list.json',JSON.stringify(this.volcano_list));
      zip.file('htdocs/offline-data/eruption_list.json',JSON.stringify(this.eruption_list));
      zip.file('htdocs/offline-data/eruption_forecast.json',JSON.stringify(this.eruption_forecast_list));
      zip.file('htdocs/offline-data/time_series_list.json',JSON.stringify(this.time_series_list));
      zip.file('htdocs/offline-data/filter_color_list.json',JSON.stringify(this.filter_color_list));
      var self = this;
      this.gettingFiles = [];
      this.pos = 0;
      // $.when(
      for(var i = 0 ; i<this.listFile.length;i++){
        
        var url = this.listFile[i];
        this.gettingFiles.push(
          this.getFile({
            url: url,
            zip: zip,
            dataType: 'blob',
            // pos: pos,
            target: this,
            success: function(data){
              if (this.url.indexOf(".exe") !== -1){
                this.zip.file(this.url,data);
              }else{
                this.zip.file("htdocs/"+this.url,data);
              }
              
            }
          })
          
        );
        // pos++;
      }
      for(var i=0;i<this.time_series_list.length;i++){
        var time_serie = this.time_series_list[i];
        var url = time_serie.url;
        // console.log(data);
        // (function(i) { // protects i in an immediately called function
        this.gettingFiles.push(
          this.getFile({
            url:url,
            zip:zip,
            dataType: 'json',
            // pos: pos,
            target: this,
            success: function(data){
              zip.file("htdocs/offline-data/"+data.sr_id+".json",JSON.stringify(data));
            }
              // console.log(data);
              
              // var percentage = Math.round((i+1)/listFile.length*50);
              // // // console.log(percentage);
              // $('#progressbar').css('width',(percentage + '%'));
              // $('#progress-detail').html(percentage + '%');
            
          })
        );
        // pos++;
      }

      $.when.apply(this,this.gettingFiles).then(function(){
        zip.generateAsync({type:"blob"}).then(function (blob) {
          saveAs(blob, "eruption.zip");
        });
      })
      
      

    }
  });
});