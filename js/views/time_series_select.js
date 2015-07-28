define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      TimeSerie = require('models/serie'),

      template = require('text!templates/time_series_select.html');
  //check/uncheck all checkboxes
  function toggle(source,selectings,timeSeries) {
    var checkboxes = document.getElementsByName(source.value);
    for (var i =0;i<checkboxes.length; i++){
     checkboxes[i].checked = source.checked;
     addSelection(checkboxes[i],selectings,timeSeries);
    }
  }
  //get category checkbox 
  //@params source: category name
  function getCategoryCheckbox(source){
    var categoryCheckboxes = document.getElementsByName("category");
    for (var i = 0; i < categoryCheckboxes.length; i++) {
      if(categoryCheckboxes[i].value == source){
        return categoryCheckboxes[i];
      }
    };
  }
  //category checkbox will be checked or unchecked depending on children checkbox
  //when source is checked or unchecked
  function categoryCheckBoxChange(source){
    var category = source.name;
    var checkboxes = document.getElementsByName(category);
    var isCheckedAll = true;
    var categoryCheckbox = getCategoryCheckbox(source.name);
    for (var i =0;i<checkboxes.length; i++){
      if(!checkboxes[i].checked){
        isCheckedAll = false;
        break;
      }
    }
    
      
      categoryCheckbox.checked = isCheckedAll;
    
  }
  // add selected time serie to shown respective graph
  function addSelection(source,selectings,timeSeries) {

    var id = $(source).val();
    if ($(source).is(':checked')){
        selectings.add(timeSeries.getTimeSerie(id));
        var x = 0;
    }
      else {
        selectings.remove(timeSeries.getTimeSerie(id));
      }
  }
  return Backbone.View.extend({
    el: '',
    className : 'mgt20',
    events: {
      'change input': 'onChange'
    },

    template: _.template(template),
    
    initialize: function(options) {
      this.volcano = options.volcano;
      this.selectings = options.selectings;
      this.observer = options.observer;
      this.timeSeries = options.timeSeries;

    },
    
    changeVolcano: function(vd_id,timeSeries) {
      if(vd_id == -1){ // when user select "Please select vocalno"
        this.$el.html(""); // no time serie appears
        this.trigger('hide');
      }else{
        timeSeries.changeVolcano(vd_id);
        this.selectings.reset();
        this.selectings.trigger('update');
      }
      

    },

    render: function(timeSeries) {
      
      this.$el.html(this.template({
        timeSeries: timeSeries.models,
      }));
    },

    onChange: function(event) {
      var input = event.target;
      
          
      if($(input).attr('name') == "category"){ // cehck category(parent) checkbox
        toggle(input,this.selectings,this.timeSeries);
        
      }else{ //check/uncheck child checkbox
        addSelection(input,this.selectings,this.timeSeries);
        categoryCheckBoxChange(input);
      }
      this.selectings.trigger('update');
      
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