define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      
      template = require('text!templates/time_series_select.html');
  //check/uncheck all function
  function toggle(source,selectings) {
    var checkboxes = document.getElementsByName(source.value);
    for (var i =0;i<checkboxes.length; i++){
     checkboxes[i].checked = source.checked;
     addSelection(checkboxes[i],selectings);
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
  function addSelection(source,selectings) {
    var id = $(source).val();
    if ($(source).is(':checked'))
        selectings.add(id);
      else 
        selectings.remove(selectings.get(id));
  }
  return Backbone.View.extend({
    el: '',

    events: {
      'change input': 'onChange'
    },

    template: _.template(template),
    
    initialize: function(options) {
      _(this).bindAll('render', 'changeVolcano');
      this.volcano = options.volcano;
      this.selectings = options.selectingTimeSeries;
      this.observer = options.observer;
      
      this.listenTo(this.volcano, 'change', this.changeVolcano);
      this.listenTo(this.collection, 'sync', this.render);
    },
    
    changeVolcano: function() {
      this.collection.changeVolcano(this.volcano.get('vd_id'));
      this.selectings.reset();

    },

    render: function() {
      
      this.$el.html(this.template({
        timeSeries: this.collection.models,
        
      }));
    },

    onChange: function(event) {
      var input = event.target;

          
      if($(input).attr('name') == "category"){
        toggle(input,this.selectings);
        
      }else{
        addSelection(input,this.selectings);
        categoryCheckBoxChange(input);
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