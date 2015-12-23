define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Filter = require('models/filter'),
      Filters = require('collections/filters'),
      template = require('text!templates/filter.html'),
      materialize = require('material');
      
  return Backbone.View.extend({
    el: '',

    className : "mgt15",

    template: _.template(template),

    events: {
      'change select': 'showGraph'
    },
    
    initialize: function(options) {
      
      this.observer = options.observer;
      this.selectingTimeSeries = options.selectingTimeSeries;
      this.selectingFilters = options.selectingFilters;
      this.filters = new Filters;
    },
    selectingTimeSeriesChanged: function(selectingTimeSeries){
      this.selectingTimeSeries = selectingTimeSeries;
      // this.filters.reset();
      if(this.selectingTimeSeries.length == 0){ 
        this.hide();
      }else{
        this.render(this.filters);  
      }
      
    },
    //this.filter is grouped by timeSerie
    getFilter: function(timeSerie){
      var data = timeSerie.attributes.data.data;
      if(data == undefined){
        return;
      }
      
      for (var i = 0; i < data.length; i++) {
        this.filters.push(timeSerie,data[i].filter);
      }

    },
    updateSelectingFilters: function(){
      /* remove timeseries which are no longer selected*/
      for(var i = 0;i<this.selectingFilters.length;i++){
        var pos = -1;
        for(var j = 0;j<this.selectingTimeSeries.length;j++){
          
          if(this.selectingTimeSeries.models[j].get('sr_id') == this.selectingFilters.models[i].timeSerie.get('sr_id')){
            pos = j;
            break;
          }
          
        }
        if(pos == -1){
            this.selectingFilters.remove(this.selectingFilters.models[i]);
            i--;
          }
      }
    },
    render: function(options) {
      this.filters.reset();
      /* get filter from selecting Time Series */
      var models = this.selectingTimeSeries.models;
      for (var i = 0; i < models.length; i++) {
        this.getFilter(models[i]);

      };
      /*and update selecting Filters*/
      this.updateSelectingFilters();

      
      /* if timeSerie has no filter ( filter = " "), select by default */
      for(var i = 0; i< this.filters.length;i++){ // go through all timeSeries
        var filter = this.filters.models[i];
        for(var j = 0;j<filter.name.length;j++){ //go through all filterName in each timeSeries
          var filterName = filter.name[j];
          if(filterName == " "){
            //select data having no filter (filter = " ")
            var timeSerie = this.selectingTimeSeries.get(filter.timeSerie.sr_id);
            this.selectingFilters.push(timeSerie,filterName);
          }
        }
      }
      this.selectingFilters.trigger('update');
      var selectingFilters = this.selectingFilters.getAllFilters();
      this.$el.html(this.template({
        filters : this.filters.models,
        selectings :this.selectingFilters
      }));
      $('.filter-select').material_select(); 
    },
    hide: function(){
      this.$el.html("");
      this.selectingFilters.reset();
      this.trigger('hide');

    },
    
    showGraph: function(event) {
        
        
      
      this.selectingFilters.reset();
      var options = $('.filter-select-option');
      // for(var i = 0; i<selects.length;i++){
      for(var i = 0;i<options.length;i++){
        var option = options[i];
        if(option.selected){
          var temp = option.value.split(".");
          this.selectingFilters.push(this.selectingTimeSeries.get(temp[0]),temp[1]);
        }
      
      }
        
      this.selectingFilters.trigger('update');
      
      
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