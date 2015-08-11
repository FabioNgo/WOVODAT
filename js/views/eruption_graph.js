define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection']),
      moment = require('moment'),
      Const = require('helper/const'),
      edTemplate = require('text!templates/tooltip_ed.html'),
      edphsTemplate = require('text!templates/tooltip_ed_phs.html'),
      TimeRange = require('models/time_range'),
      Tooltip = require('views/eruption_tooltip');

  return Backbone.View.extend({
    el: '',
    
    initialize: function(options) {
      _(this).bindAll(
        // 'render',
        'onHover'
        // 'updateStartTime',
        // 'changeTimeRange'
      );
      this.observer = options.observer;
      //this.eruptions = options.eruptions;
      this.timeRange = new TimeRange();
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
      // this.eruptions = new Array();
      this.selectingEruption = options.selectingEruption;
      this.edTooltip = new Tooltip({
        template: edTemplate
      });
      this.edphsTooltip = new Tooltip({
        template: edphsTemplate
      });
    },

    onHover: function(event, pos, item) {
      if (!item) {
        this.edTooltip.hide();
        this.edphsTooltip.hide();
      } else if (item.series.dataType === 'ed'){
        this.edTooltip.update(pos, item);
        this.edphsTooltip.hide();
      } else {
        this.edphsTooltip.update(pos, item);
        this.edTooltip.hide();
      }
    },

    changeEruption: function(selectingEruption){
      if(selectingEruption.get('ed_id') == -1){
        this.hide();
      }else{
        this.selectingEruption = selectingEruption;
        this.show();
      }

    },
    //show eruption graph
    show: function(){
      this.render();
      this.trigger('show');
    },
    //hide eruption graph
    hide: function(){
      this.selectingEruption = undefined;
      this.$el.html("");
      this.$el.height(0);
      this.$el.width(0);
      this.trigger('hide');
    },
    gernerateBarChartFlotData: function(data,color,label,barWidth,dataType,name){
      return {
        data: [data],
        color: color,
        label: label,
        bars:{
          show: true,
          barWidth: barWidth,
          

        },
        dataType: dataType,
        name: name,
        startTime: data[0],
        endTime: data[0]+barWidth,
      }
    },
    render: function() {
      if(this.selectingEruption == undefined){
        return;
      }

      var maxVEI = 7;
      var self = this,
          
          el = this.$el,
          data = this.prepareData(),
          graph_pram_data = [],         
          option = {
            grid: {
              hoverable: true,
            },
            xaxis: {
              min: this.startTime,
              max: this.endTime,
              autoscale: true,
              mode: 'time',
              timeformat: "%d-%b-%Y",
            },
            yaxis: {
              min: 0,
              max: maxVEI +1,
              tickSize: 1,
              panRange: false,
              zoomRange: false,
              tickFormatter: function(val, axis) { return val < axis.max ? val.toFixed(0) : 'VEI'; },
              labelWidth: 30
            },
            
            pan: {
              interactive: false
            },
            zoom: {
              interactive: true
            }
          };
      
      /** Phreatic Eruption **/
      var temp = data.ed_phs_data;
      for(var i =0;i<temp.length;i++){
        if(i==0){
          graph_pram_data.push(this.gernerateBarChartFlotData(temp[i].data,'#F44336','Eruption Phase',temp[i].duration,'ed_phs',temp[i].type));
        }else{
          graph_pram_data.push(this.gernerateBarChartFlotData(temp[i].data,'#F44336',undefined,temp[i].duration,'ed_phs',temp[i].type));
        }
      }
      /** Eruption part **/
      graph_pram_data.push(this.gernerateBarChartFlotData (data.edData.data, 'Gray','Eruption', data.edData.duration,'ed',""));
      el.width('auto');
      el.height(150);
      el.addClass("eruption-graph");
      this.graph = $.plot(el, graph_pram_data, option);
      var eventData = {
        startTime: this.startTime,
        endTime: this.endTime,
        data: graph_pram_data,
        graph: this.graph,
        el: this.$el,
        original_option: option
      };
      el.bind('plothover', this.onHover);
      el.bind('plotzoom', eventData,this.onZoom);
    },
    onZoom: function(event,plot){
      var option = event.data.original_option;
      var xaxis = plot.getXAxes()[0];
      var data = event.data.data;
      /* The zooming range cannot wider than the original range */
      if(xaxis.min<event.data.startTime || xaxis.max > event.data.endTime){
        option.xaxis.min = event.data.startTime;
        option.xaxis.max = event.data.endTime;
        event.data.graph = $.plot(event.data.el,data,option);
      }
    },
    getStartingTime: function(ed_stime){
      var date = new Date(ed_stime);
      var year = date.getFullYear();
      var starting_date =  new Date(year,0,0,0,0,0,0);
      return starting_date.getTime();

    },
    prepareData: function() {
      var self = this,
          edData,
          ed_phs_data = [];

        if(this.selectingEruption == undefined){ // no eruption is selected
          return;
        }

        var ed = this.selectingEruption;
        // console.log(ed);
        var ed_stime = ed.get('ed_stime'),
            ed_etime = ed.get('ed_etime'),
            ed_vei = ed.get('ed_vei');
        var start_date = new Date(ed_stime);
        this.startTime = this.getStartingTime(ed_stime);
        this.endTime = this.startTime+ Const.ONE_YEAR;
        this.serieGraphTimeRange.set({
          'startTime': this.startTime,
          'endTime': this.endTime,
        });
        // console.log(this.serieGraphTimeRange);
        
        this.serieGraphTimeRange.trigger('update',this.serieGraphTimeRange);
        this.forecastsGraphTimeRange.set({
          'startTime': this.startTime,
          'endTime': this.endTime,
        });
        // console.log(this.serieGraphTimeRange);
        
        this.forecastsGraphTimeRange.trigger('update',this.forecastsGraphTimeRange);
        edData = {
          data: [ed_stime, 0,ed_vei],
          0: 0,
          duration: ed_etime - ed_stime,
          attributes: ed.attributes
        };
        // endOfTime = Math.max(endOfTime, ed_stime + Const.ONE_YEAR);

        ed.get('ed_phs').forEach(function(ed_phs) {
          
          var ed_phs_stime = ed_phs.ed_phs_stime,
              ed_phs_etime = ed_phs.ed_phs_etime,
              ed_phs_duration = ed_phs_etime - ed_phs_stime,
              ed_phs_lower_vei = undefined,
              ed_phs_upper_vei = undefined,
              ed_phs_type = ed_phs.ed_phs_type;
          /** Phereatic Eruption is vertical bar **/
          if(ed_phs_type == "Phreatic eruption"){
            ed_phs_lower_vei = 0;
            ed_phs_upper_vei = 1;
          }
          /** Magmatic Extrusion is horizontal bar **/
          if(ed_phs_type == "Magmatic extrusion"){
            ed_phs_lower_vei = 0.5-0.2;
            ed_phs_upper_vei = 0.5+0.2;
          }
          /** Tectonic Earthquake is vertical bar **/
          if(ed_phs_type == "Tectonic earthquake"){
            ed_phs_lower_vei = 0;
            ed_phs_upper_vei = 1;
          }
          /** Explosion is vertical bar **/
          if(ed_phs_type == "Explosion"){
            ed_phs_lower_vei = 0;
            ed_phs_upper_vei = ed_phs.ed_phs_vei;
          }
          /** Climatic phase is vertical bar **/
          if(ed_phs_type=="Climatic phase"){
            ed_phs_lower_vei = 0;
            ed_phs_upper_vei = ed_phs.ed_phs_vei;
          }
          ed_phs_data.push({
            data: [ed_phs_stime,ed_phs_lower_vei,ed_phs_upper_vei],
            duration: ed_phs_duration,
            type: ed_phs_type
          });
        });
      // });

      return {
        edData: edData,
        ed_phs_data: ed_phs_data
        
      };
    }
  });
});