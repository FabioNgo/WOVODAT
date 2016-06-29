define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      TimeSerie = require('models/serie'),

      template = require('text!templates/time_series_select.html'),
      loading = require('text!templates/loading.html'),
      Handlebars = require('handlebars'),
        materialize = require('material');
  
  return Backbone.View.extend({
    el: '',
    className : 'mgt20',
    events: {
      'change select': 'selectChangedHandler',
      'change input': 'selectChangedHandler'
    },

    // template: _.template(template),
    // loading: _.template(loading),
    
    initialize: function(options) {
      this.volcano = options.volcano;
      this.selectingTimeSeries = options.selectingTimeSeries;
      this.observer = options.observer;
      this.timeSeries = options.timeSeries;
      this.categories = options.categories;
      this.selectingFilters = options.selectingFilters;
      this.selectedTimeSeries = options.selectedTimeSeries;
      this.selectingTimeSeries.reachLimit = function(){
        return (this.length >= 5);
      }
    },
    showLoading: function(){
      this.$el.html(this.loading);
    },
    changeVolcano: function(vd_id,timeSeries) {
      this.showLoading();
      if(vd_id == -1){ // when user select "Please select vocalno"
        this.$el.html(""); // no time serie appears
        this.trigger('hide');
      }else{
        timeSeries.changeVolcano(vd_id);
        this.selectingTimeSeries.reset();
        this.selectingTimeSeries.trigger('update');
      }
      
    },

    render: function(timeSeries) {

      this.$el.html("");
      var container =$("<div></div>");
      container.addClass("time_series_select_container card-panel");
      this.$el.append(container);
      
      // console.log(timeSeries);
      var temp = Handlebars.compile(template);
      Handlebars.registerHelper('list', function(items, options) {
        var ret = "";
        for(var i=0, j=items.length; i<j; i++) {
          ret = ret+options.fn(items[i]);
        }
        if(ret == ""){
          ret = "No data";
        }
        return ret;
      });

      var options = {
        timeserie: this.generateCategories(timeSeries)
      }
      var html = temp(options);
      $('.time_series_select_container').append(html);
      $('.time-serie-select').material_select();
      if(this.selectedTimeSeries != undefined){
        this.showFilter();
      }
      
    },
    //generate Categories for html template
    //output: [{title,data}]
    generateCategories: function(timeSeries){
      var output=[];
      var categories=this.categories;
      for(var i =0;i<categories.length;i++){
        var category = timeSeries[categories[i]];
        if(category!=undefined){
          output.push({
            category:categories[i],
            data: this.generateData(category)
          })
        }
      }
      
      return output;
    },
    //generate Data for html template
    /* output: {[{sr_id,station1,station2,component}]} */
    generateData: function(items){
      var output = [];
      var station1 = "";
      var station2 = "";
      for(var i=0;i<items.length;i++){
        var item = items[i].attributes;
        
        output.push({
          sr_id:item.sr_id,
          showingName: item.showingName
        })
      }
      return output;
    },
    selectChangedHandler: function(event){

      if(event.target.classList[0]=="time-serie-select"){
        
       
          this.showFilter();
        
      }else{
        this.trigger("filter-select-change");
      }
    },
    showFilter: function(event) {
        
        
      // this.$el.append(this.loading);
      
      this.selectingTimeSeries.reset();
      
      var options = $('.time-serie-select-option');
      var temp =[];
      for(var i = 0; i<this.selectedTimeSeries.length;i++){
        temp.push(this.timeSeries.get(this.selectedTimeSeries[i]));
      }
      for(var i = 0;i<options.length;i++){
          var option = options[i];
          //check the timeseries selected from url
          var pos = undefined;
          for(var j = 0; j<temp.length;j++){
            if(temp[j]!=undefined){
              if(temp[j].get('sr_id') == option.value){
                option.selected = true;
                pos = i;
              }
            }
          }
          if(option.selected){
            if(pos!=undefined){
              this.selectingTimeSeries.add(temp[pos]);
            }else{
              this.selectingTimeSeries.add(this.timeSeries.get({sr_id:option.value}));
            }
          }
      }
      
      // set limitation of 
      var groupItems = $('.multiple-select-dropdown');
      for(var i=0;i<groupItems.length;i++){
        var items = groupItems[i].children;
        //dont traverse the first item
        for(var j=1;j<items.length;j++){
          var item = items[j];
          //check if item is active or disable or not
          var isActive = false;
          var isDisabled = false;
          for(var k = 0; k < item.classList.length;k++){
            if(item.classList[k] == 'active'){
              isActive = true;
            }
            if(item.classList[k] == 'disabled'){
              isDisabled = true;
            }
          }
          if(!isActive && this.selectingTimeSeries.reachLimit()){
            item.classList.add('disabled');
            //disable checkbox
            item.children[0].children[0].disabled = true;
          }
          if(isDisabled&& !this.selectingTimeSeries.reachLimit()){
            item.classList.remove('disabled');
            //enable checkbox
            
            item.children[0].children[0].disabled = false;
          }
        }
        
        // $('.time-serie-select').remove('.select-dropdown','.caret');

        // $('.time-serie-select').material_select();
      }
      this.selectingTimeSeries.trigger("change");
      $('.time-serie-select').material_select('destroy');
      $('.time-serie-select').material_select();

    },
    

    destroy: function() {
      // From StackOverflow with love.
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});