define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Eruption = require('models/eruption'),
      template = require('text!templates/eruption_select.html'),
      materialize = require('material');
          
  return Backbone.View.extend({
    template: _.template(template),

    events: {
      'change select': 'onChangeEruption'
    },
    
    initialize: function(options) {
      _(this).bindAll('render', 'changeEruption');
      
      this.observer = options.observer;
      this.selectingEruptions = options.selectingEruptions;
      this.eruptions = options.eruptions;
      this.eruptionForecasts = options.eruptionForecasts;
      this.availableEruptions = [];
      this.selecting_vd_num = options.selecting_vd_num;
      this.ed_stime_num = options.ed_stime_num;
      this.ed_etime_num = options.ed_etime_num;
      this.selectingTimeRange = options.selectingTimeRange;
      // this.setUpTimeRange(this.ed_stime_num,this.ed_etime_num);
      //this.showEruption();
      //console.log(this.selectingEruptions);

    },
    

    fetchEruptions: function(vd_id) {
      this.eruptions.changeVolcano(vd_id);
      this.eruptionForecasts.changeVolcano(vd_id);
      this.availableEruptions = this.eruptions.getAvailableEruptions(this.selectingTimeRange);
      //console.log(this.eruptions);
      // this.show(); // newly changed
    },

    changeEruption: function(selectingEruption) {
      this.$el.find('select').val(selectingEruption.get('ed_id'));
      this.$el.find('select').change();
    },
    selectingTimeRangeChanged: function(timeRange){
      // only show eruptions within that timeRange
      this.availableEruptions = this.eruptions.getAvailableEruptions();
      var eruptionsWithinTimeRange = this.eruptions.getAvailableEruptions(timeRange);
      if(eruptionsWithinTimeRange.length == 0){
        this.availableEruptions.notAvailable = 1;
      }
      else{
        this.availableEruptions = eruptionsWithinTimeRange;
        this.availableEruptions.notAvailable = 0;
      }
      this.show();
    },
    eruptionsFetched : function(){
      var ed_stime = this.ed_stime_num;
      var ed_etime = this.ed_etime_num;
      this.availableEruptions = this.eruptions.getAvailableEruptions();
      if(ed_stime == undefined && ed_etime == undefined){
        this.show();
      }
      else{
        var ed_id;
        for(var i = 0; i<this.availableEruptions.length; i++){
          var current_model = this.availableEruptions[i];
          if(current_model.attributes.ed_stime == ed_stime || current_model.attributes.ed_etime == ed_etime){
            ed_id = current_model.id;
            break;
          }
        }
        this.selectingEruptions.reset();
        this.selectingEruptions.add(this.eruptions.get(ed_id));
        this.render();
      }
    },
    render: function() {
      this.$el.html("");

      var selectingEruption = this.selectingEruptions.models[0];
      
      this.$el.html(this.template({
        eruptions: this.availableEruptions,
        eruptionsNotAvailable: this.availableEruptions.notAvailable,
        selectingEruption: selectingEruption
      }));
      $('.eruption-select').material_select();
    },

    onChangeEruption: function() {
      var ed_id = this.$el.find('select').val();
      // if(ed_id)
      // var startTime = this.collection.get(ed_id).get('ed_stime');
      this.selectingEruptions.reset();
      if(ed_id == -1){
        this.selectingEruptions.add(new Eruption({'ed_id':-1})); // select ----
      }else{
        this.selectingEruptions.add(this.eruptions.get(ed_id));  
      }
      //console.log(this.selectingEruptions);
      var ed_etime = this.selectingEruptions.models[0].attributes.ed_etime;
      var ed_stime = this.selectingEruptions.models[0].attributes.ed_stime;
      var hash = Backbone.history.location.hash;
      hash = hash.substring(1);
      var params=hash.split('&');
      var options  =[];
      for(var i = 0 ;i< params.length;i++){
        var keyVal = params[i].split('=');
        
        if(!(keyVal[0] == "ed_stime" || keyVal[0] == 'ed_etime')){
          options.push(params[i]);
        }
        
      }
      hash = "";
      for(var i = 0;i<options.length;i++){
        if(i!=0){
          hash += '&';
        }
        hash += options[i];
      }
      hash = hash + "&ed_stime="+ed_stime+"&ed_etime="+ed_etime;
      Backbone.history.navigate(hash);
      // this.selectingEruption.set('ed_id', ed_id);
      // this.selectingEruption.trigger('change');
      // this.observer.trigger('change', this.selectingEruption);
    },

    //hide eruption_select from page
    hide: function(){
      this.$el.html("");
      this.selectingEruptions.length = 0;
      this.trigger('hide');
    },

    // show eruption_select on page
    show: function(){
      // this.fetchEruptions(this.volcano);
      
      // this.fetchEruptions();
      this.selectingEruptions.length = 0;
      this.selectingEruptions.add(new Eruption({'ed_id':-1})); // select ----
      this.render();
    },

    //when no filter select, eruption not appear
    selectingFiltersChanged: function(selectingFilters) {
      this.availableEruptions = this.eruptions.getAvailableEruptions();
      //console.log(this.eruptions);
      // if (selectingFilters.length == 0) {
      //   this.hide();
      // }else{
      //   this.show();
      // }
      this.show();
    },

    // showEruption: function(){
    //   this.availableEruptions = this.eruptions.getAvailableEruptions();
    //   console.log(this.eruptions);
    // },

    destroy: function() {
      // From StackOverflow with love.
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});