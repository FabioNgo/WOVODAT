define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Filter = require('models/filter'),
      Filters = require('collections/filters'),
      template = require('text!templates/filter.html');
      
  return Backbone.View.extend({
    el: '',

    className : "mgt15",

    template: _.template(template),

    events: {
      'change input': 'onChange'
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
    getFilter: function(timeSerie){
      var data = timeSerie.get('data').data;
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

      var filters = this.filters.getAllFilters();
      /* if timeSerie has no filter ( filter = " "), select by default */
      for(var i = 0; i< filters.length;i++){
        if(filters[i].filter == " "){
          var timeSerie = this.selectingTimeSeries.getTimeSerie(filters[i].timeSerie);
          this.selectingFilters.push(timeSerie,filters[i].filter);
        }
      }
      this.selectingFilters.trigger('update');
      var selectingFilters = this.selectingFilters.getAllFilters();
      /* check selected filters */
      for(var i =0;i<filters.length;i++){
        for(var j=0;j<selectingFilters.length;j++){
          if(filters[i].name == selectingFilters[j].name && filters[i].timeSerie == selectingFilters[j].timeSerie){
            filters[i].isChecked = true;
            break;
          }else{
            filters[i].isChecked = false;
          }
        }
      }
      this.$el.html(this.template({
        filters : filters
      }));
    },
    hide: function(){
      this.$el.html("");
      this.selectingFilters.reset();
      this.trigger('hide');

    },
    
    
    onChange: function(event) {
      var input = event.target,
          value = $(input).val();
      var timeSerie = this.selectingTimeSeries.getTimeSerie(value);
      var filter = $(input).attr('name');
      if ($(input).is(':checked')) {
        
        this.selectingFilters.push(timeSerie,filter);
      }
      else {
        this.selectingFilters.pop(timeSerie,filter);
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