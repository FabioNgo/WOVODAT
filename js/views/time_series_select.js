define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      TimeSerie = require('models/serie'),

      template = require('text!templates/time_series_select.html');
  
  return Backbone.View.extend({
    el: '',

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
    // After select the Type of Data by checking the check box then the data of that catergory will be updated.
    // updateVolcanoData: function (vd_id,timeSeries) {
    //   timeSeries.updateData(vd_id);
    //   this.selectings.reset();
    //   this.selectings.trigger('update');
    // },

    render: function(timeSeries) {
      
      this.$el.html(this.template({
        timeSeries: timeSeries.groupedData,
      }));
      $('select').material_select();
    },

    onChange: function(event) {
      var input = event.target;
      
          
      if($(input).attr('name') == "category"){ // check category(parent) checkbox
        toggle(input,this.selectings,this.timeSeries);
        
      }else{ //check/uncheck child checkbox
        addSelection(input,this.selectings,this.timeSeries);
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