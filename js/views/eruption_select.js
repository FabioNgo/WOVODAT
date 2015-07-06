define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Eruption = require('models/eruption'),
      template = require('text!templates/eruption_select.html');
          
  return Backbone.View.extend({
    el: '',

    template: _.template(template),

    events: {
      'change select': 'onChangeEruption'
    },
    
    initialize: function(options) {
      _(this).bindAll('render', 'changeEruption');
      
      this.observer = options.observer;
      this.selectingEruptions = options.selectingEruptions;
      this.collection = options.collection;
    },
    
    fetchEruptions: function(vd_id) {
      this.collection.changeVolcano(vd_id);
      
    },

    changeEruption: function(selectingEruption) {
      this.$el.find('select').val(selectingEruption.get('ed_id'));
      this.$el.find('select').change();
    },

   
    render: function() {
      var selectingEruption = this.selectingEruptions.models[0];
      this.$el.html(this.template({
        eruptions: this.collection.models,
        selectingEruption: selectingEruption
      }));
    },

    onChangeEruption: function() {
      var ed_id = this.$el.find('select').val();
      // if(ed_id)
      // var startTime = this.collection.get(ed_id).get('ed_stime');
      this.selectingEruptions.reset();
      if(ed_id == -1){
        this.selectingEruptions.add(new Eruption({'ed_id':-1})); // select ----
      }else{
        this.selectingEruptions.add(this.collection.get(ed_id));  
      }
      
      // this.selectingEruption.set('ed_id', ed_id);
      // this.selectingEruption.trigger('change');
      // this.observer.trigger('change', this.selectingEruption);
    },

    //hide eruption_select from page
    hide: function(){
      this.$el.html("");
      this.trigger('hide');
    },

    // show eruption_select on page
    show: function(){
      // this.fetchEruptions(this.volcano);
      
      // this.fetchEruptions();
      
      this.render();
    },

    //when no series select, eruption not appear
    timeSeriesChanged: function(selectingTimeSeries) {
      if (selectingTimeSeries.length == 0) {
        this.hide();
      }else{
        this.show();
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