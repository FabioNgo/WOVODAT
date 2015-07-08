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
      this.filters.reset();
      if(this.selectingTimeSeries.length == 0){
        this.hide();
      }else{
        this.render(this.filters);  
      }
      
    },
    getFilter: function(timeSerie){
      var data = timeSerie.get('data');
      if(data == undefined){
        return;
      }
      
      for (var i = 0; i < data.length; i++) {
        this.filters.push(timeSerie,data[i].filter);
      }

    },
    render: function(options) {
      var models = this.selectingTimeSeries.models;
      for (var i = 0; i < models.length; i++) {
        this.getFilter(models[i]);
      };

      this.$el.html(this.template({
        filters : this.filters.getAllFilters()
      }));
    },
    hide: function(){
      this.$el.html("");
    },
    prepareDataAndRender: function() {
      var options = {}, data = this.model.get('data');
      options = this.OptionForFilter();
      this.render(options);
      if ( options.length == 0 ) {
        this.filter.add({filter : undefined});
      }

    },
    
    OptionForFilter: function() {
      var data = this.model.get('data'),      
        list=[],
        a = [];
      data.forEach( function(ds) {
        if ( ds.filter && _.indexOf( list, ds.filter) == -1 ) 
          list.push(ds.filter); 
      });

      return list;
    },
    
    onChange: function(event) {
      var input = event.target,
          value = $(input).val();
      if ($(input).is(':checked')) {
        this.filter.add( { filter : value } );
      }
      else {
        this.filter.remove(this.filter.get(value));
      }
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