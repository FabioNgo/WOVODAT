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
      Tooltip = require('views/tooltip');

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
      // this.eruptions = new Array();
      this.selectingEruption = options.selectingEruption;
      this.edTooltip = new Tooltip({
        template: edTemplate
      });
      this.edphsTooltip = new Tooltip({
        template: edphsTemplate
      });
      // this.listenTo(this.eruptions, 'sync', this.render);
      // this.listenTo(this.observer, 'change-start-time', this.updateStartTime);
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

    // updateStartTime: function(startTime) {
    //   this.startTime = startTime;
    //   this.render();
    // },
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
    render: function() {
      if(this.selectingEruption == undefined){
        return;
      }
      var self = this,
          el = this.$el,
          data = this.prepareData(),
          // endOfTime = this.startTime ? this.startTime + Const.ONE_YEAR : data.endOfTime,
          param_ed = {
            data: data.edData,
            color: 'Gray',
            label: 'Eruption',
            bars: {
              show: true,
              wovodat: true
            },
            dataType: 'ed'
          },
          param_ed_phs = {
            data: data.ed_phsData,
            label: 'Eruption phase',
            color: '#F44336',
            bars: {
              show:true,
              wovodat: true,
              lineWidth: 0.5,
              drawBottom: true
            },
            dataType: 'ed_phs'
          },
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
              max: 6,
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
              interactive: false
            }
          };
          
      el.width('auto');
      el.height(150);
      el.addClass("eruption-graph");
      this.graph = $.plot(el, [param_ed, param_ed_phs], option);

      el.bind('plothover', this.onHover);
      // el.bind('plotpan', this.changeTimeRange);
      el.bind('plotzoom', this.changeTimeRange);
      // this.changeTimeRange();
    },

    // changeTimeRange: function() {
    //   var startTime = this.graph.getAxes().xaxis.options.min,
    //       endTime = this.graph.getAxes().xaxis.options.max;
    //   // this.timeRange.set({
    //   //   'startTime': startTime,
    //   //   'endTime': endTime
    //   // });
    // },
    selectingTimeRangeChanged: function(TimeRange){
      // if(TimeRange == undefined){
      //   return;
      // }
      // this.startTime = TimeRange.get('startTime');
      // this.endTime = TimeRange.get('endTime');
      // this.render();
    },
    getStartingTime: function(ed_stime){
      var date = new Date(ed_stime);
      var year = date.getFullYear();
      var starting_date =  new Date(year,0,0,0,0,0,0);
      return starting_date.getTime();

    },
    prepareData: function() {
      var self = this,
          edData = [],
          ed_phsData = [],
          endOfTime = 0;

      // this.eruptions.forEach(function(ed) {

        if(this.selectingEruption == undefined){ // no eruption is selected
          return;
        }
        var ed = this.selectingEruption;

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
        console.log(this.serieGraphTimeRange);
        this.serieGraphTimeRange.trigger('update',this.serieGraphTimeRange);
        edData.push([ed_stime, ed_vei, 0, ed_etime - ed_stime, ed.attributes]);

        // endOfTime = Math.max(endOfTime, ed_stime + Const.ONE_YEAR);

        ed.get('ed_phs').forEach(function(ed_phs) {
          var ed_phs_stime = ed_phs.ed_phs_stime,
              ed_phs_etime = ed_phs.ed_phs_etime,
              ed_phs_vei = ed_phs.ed_phs_vei;

          ed_phsData.push([ed_phs_stime, ed_phs_vei, ed_phs_vei - 0.2, ed_phs_etime - ed_phs_stime, ed_phs]);
        });
      // });

      return {
        edData: edData,
        ed_phsData: ed_phsData
        
      };
    }
  });
});