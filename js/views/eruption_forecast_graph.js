define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      Const = require('helper/const'),
      ed_phs_forTemplate = require('text!templates/tooltip_ed_phs_for.html'),
      Tooltip = require('views/eruption_tooltip');

  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      _(this).bindAll('onHover');
      
      this.observer = options.observer;
      this.timeRange = options.timeRange;

      this.tooltip = new Tooltip({
        template: ed_phs_forTemplate
      });
      this.eruptionForecasts = options.eruptionForecasts;
      this.data = this.prepareData();
        
    },

    previousHover: {
      dataIndex: null,
      savedContent: null
    },
    
    onHover: function(event, pos, item) {
      this.tooltip.update(pos, item);
    },

    // onDataChange: function() {
    //   // Prepares data.
    //   var data = this.collection.models,
    //       ed_forData = [];

    //   data.forEach(function(ed_for) {
    //     var ed_for_astime = ed_for.get('ed_for_astime'),
    //         ed_for_aetime = ed_for.get('ed_for_aetime');
    //     ed_forData.push([ed_for_astime, 2, 0, ed_for_aetime - ed_for_astime, ed_for.attributes]);
    //   });

    //   // Saves prepared data to the view object.
    //   this.data = ed_forData;

    //   this.render({
    //     startTime: this.startTime,
    //     endTime: this.endTime,
    //     data: this.data
    //   });
    // },

    changeEruption: function(selectingEruption){
      if(selectingEruption.get('ed_id') == -1){
        this.hide();
      }else{
        this.selectingEruption = selectingEruption;
        this.show();
      }

    },
    forecastsGraphTimeRangeChanged: function(timeRange){
      this.startTime = timeRange.get('startTime');
      this.endTime = timeRange.get('endTime');
      this.render();
      
    },
    
    //show eruption forecast graph
    show: function(){
      this.render();
    },
    //hide eruption cast graph
    hide: function(){
      this.selectingEruption = undefined;
      this.$el.html("");
      this.$el.height(0);
      this.$el.width(0);
    },
    prepareData: function() {
      var self = this,
          ed_forData = [],
          ed_forDataType = [];

      
      // if(this.eruptionForecasts == undefined){
      //   return;
      // }
      var data = this.eruptionForecasts.models;

      ed_forData = [];
      ed_forDataType = [];
      data.forEach(function(ed_for) {
        var ed_for_astime = ed_for.get('ed_for_astime'),
            ed_for_aetime = ed_for.get('ed_for_aetime'),
            ed_for_type = ed_for.get('ed_for_alevel');
        ed_forData.push([ed_for_astime,ed_for_aetime,0,1]);
        ed_forDataType.push(ed_for_type);
      });

      // Saves prepared data to the view object.
      
      return {
        ed_forData: ed_forData,
        ed_forDataType: ed_forDataType
                }; 
      
    },
    gernerateBarChartFlotData: function(data,color,label,dataType,name){
      return {
        data: data,
        color: color,
        label: label,
        bars:{
          show: true,
          fullparams: true,
          horizontal:true,
          

        },
        dataType: dataType,
        name: name,
      }
    },
    render: function(options) {
      var el = this.$el,
          data = this.data.ed_forData,
          option = {
            grid: {
              hoverable: true
            },
            xaxis: {
              min: this.startTime,
              max: this.endTime,
              autoscale: true,
              mode: 'time',
              timeformat: '%d-%b-%Y'
            },
            yaxis: {
              show:true,
              ticks: [0,1],
              min:0,
              max:1,
              labelWidth: 30,
              panRange: false
            }
          };
      var graph_pram_data = [];
      
      graph_pram_data.push(this.gernerateBarChartFlotData(data,'#F44336','Alert Level','ed_for',""));
        
      
      el.width('auto');
      el.height(150);
      el.addClass("eruption-forecasts-graph card-panel");

      $.plot(el, graph_pram_data, option);
      el.bind('plothover', this.onHover);
    },
  });
});