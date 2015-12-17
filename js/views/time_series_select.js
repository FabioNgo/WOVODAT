define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      TimeSerie = require('models/serie'),

      template = require('text!templates/time_series_select.html'),
        materialize = require('material');
  
  return Backbone.View.extend({
    el: '',

    events: {
      'change select': 'showFilter'
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
      $('.time-serie-select').material_select();
      // $('#showGraphBtn').click(function(){
      //   this.showGraph());
      // });
      
    },

    showFilter: function(event) {
        
        
      
      this.selectings.reset();
      
      var options = $('.time-serie-select-option');
      
      for(var i = 0;i<options.length;i++){
          var option = options[i];
          if(option.selected){
            this.selectings.add(this.timeSeries.get(option.value));
          }
        
      }
      this.selectings.trigger("change");
      
      
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