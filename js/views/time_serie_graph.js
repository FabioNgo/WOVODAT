define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection', 'jquery.flot.errorbars']),
      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/series_tooltip'),
      TimeRange = require('models/time_range'),
      GraphHelper = require('helper/graph'),
      DateHelper = require('helper/date');

  return Backbone.View.extend({    
    initialize: function(options) {
      this.filters = options.filters;
      this.eruptionTimeRange = options.eruptionTimeRange;
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
      this.timeRange = new TimeRange();
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      this.prepareData();
    },

    timeRangeChanged: function(TimeRange){
      if(TimeRange == undefined){
        return;
      }
      this.minX = TimeRange.get('startTime');
      this.maxX = TimeRange.get('endTime');
      // this.render();
    },

    onHover: function(event, pos, item) {
      var tooltip = event.data;
      tooltip.update(pos, item);
    },
    show: function(){
      
      // this.timeRangeChanged(this.timeRange);
      this.render();
    },
    render: function() {
      if(this.data==undefined){
        return;
      }
      this.$el.html("");
      var options = {
            // series: {
            //   points:{
            //     show: true,
            //     radius: 5,
            //     lineWidth: 2, // in pixels
            //     fill: true,
            //     fillColor: null,
            //     symbol: "circle" 
            //   },
            //   lines:{
            //     show: false
            //   },

            // },
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b-%Y",
              autoscale: true,
              min: this.minX,
              max: this.maxX
            },
            yaxis: {
              show: true,
              min: this.minY,
              ticks: this.ticks,
              labelWidth: 30,
              zoomRange: false,
            },
            grid: {
              hoverable: true,
            },
            zoom: {
              
              interactive: true,
              
            },
            tooltip:{
              show: true,
            },
            
          };
          
      if (!this.data || !this.data.length) {
        this.$el.html('');
        return;
      }
      // console.log(this.data);
      this.$el.width('auto');
      this.$el.height(200);
      this.$el.addClass('time-serie-graph');
      // plot the time series graph after being selected (eg. onSelect in OverViewGraph).
      // config graph theme colors
      options.colors = ["#000000", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"];
      this.graph = $.plot(this.$el, this.data, options);
      this.$el.bind('plothover', this.tooltip,this.onHover);
      var eventData = {
        startTime: this.minX,
        endTime: this.maxX,
        data: this.data,
        graph: this.graph,
        el: this.$el,
        self: this,
        original_option: options
      }
      this.$el.bind('plotzoom',eventData, this.onZoom);
    },
    onZoom: function(event,plot){
      var option = event.data.original_option;
      var xaxis = plot.getXAxes()[0];
      var data = event.data.data;
      var self = event.data.self;
      /* The zooming range cannot wider than the original range */
      if(xaxis.min<event.data.startTime || xaxis.max > event.data.endTime){
        option.xaxis.min = event.data.startTime;
        option.xaxis.max = event.data.endTime;

        event.data.graph = $.plot(event.data.el,data,option);
        self.setUpTimeranges(option.xaxis.min,option.xaxis.max);
      }else{
        self.setUpTimeranges(xaxis.min,xaxis.max);
      }

    },
    setUpTimeranges: function(startTime, endTime){
      // this.serieGraphTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // // console.log(this.serieGraphTimeRange);
      
      // this.serieGraphTimeRange.trigger('update',this.serieGraphTimeRange);
      // this.forecastsGraphTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // this.forecastsGraphTimeRange.trigger('update',this.forecastsGraphTimeRange);
      // this.eruptionTimeRange.set({
      //   'startTime': startTime,
      //   'endTime': endTime,
      // });
      // this.eruptionTimeRange.trigger('update',this.eruptionTimeRange);


    },
    prepareData: function() {
      if(this.filters == undefined){
        this.data = undefined;
        return;
      }
      var minX = undefined,
          maxX = undefined,
          minY = undefined,
          maxY = undefined,
          data = [],
          errorbars = undefined,
          i;
      
      

      for(var j = 0; j<this.filters.name.length;j++){ 
        var list = [];
        var filterData = this.filters.timeSerie.getDataFromFilter(this.filters.name[j])
        var style = this.filters.timeSerie.get('data').style; //get the plot presentation style (ie. bar, dot, circle)
        filterData.forEach(function(d) {
          //var time = d.time;
          var value = d['value'];
          var maxTime;
          var minTime;
          var upperBound = undefined;
          var lowerBound = undefined;
          d.time_formated = DateHelper.formatDate(d.time);
          var error = parseFloat(d.error);
          if(d.error === undefined){
            error = 0;
          }
          if(style == 'bar'){
              maxTime = d['etime'];
              minTime = d['stime'];
          }
          else if(style == 'horizontalbar'){
            if(d.stime == undefined || d.etime == undefined){
                maxTime = minTime = d['time'];
              }
              else{
                maxTime = d['etime'];
                minTime = d['stime'];
                upperBound = value+0.5; // giving upper bound a buffer of +0.5 unit to present the horizontal bar
                lowerBound = value-0.5;  // giving lower bound a buffer of -0.5 unit to present the horizontal bar
              };
          }
          else if(style == 'dot' || style == 'circle'){
              maxTime = minTime = d['time'];
          };

          if (minX === undefined || minTime < minX){
            minX = minTime;
          }
          if (maxX === undefined || maxTime > maxX){
            maxX = maxTime;
          }
          if (minY === undefined || value-error < minY){
            minY = value-error;
          }
          if (maxY === undefined || value+error > maxY){
            maxY = value+error;
          }

          if(style == 'dot' || style == 'circle'){
            if(d['error']!=undefined){
                list.push([d['time'],d['value'],d['error']]); 
                errorbars = "y";
              }else{
                list.push([d['time'],d['value']]);  
              }
          }
          else if(style == 'bar'){
            if(d['error']!=undefined){
                list.push([d['stime'],d['etime'],d['value'],d['error']]); 
                errorbars = "y";
              }else{
                list.push([d['stime'],d['etime'],d['value']]);  
              }
          }
          else if(style == 'horizontalbar'){
            if(d['error']!=undefined){
                list.push([minTime,maxTime,lowerBound,upperBound,d['error']]); 
                errorbars = "y";
              }else{
                list.push(minTime,maxTime,lowerBound,upperBound);
              };
          };
        });
        // this.data contains the setting of options of the graph (ie. point,line,bar).
        // this makes the options in the $.plot(this.$el, this.data, options) 
        // cannot fully config the appearance of the graph.
        data.push(GraphHelper.formatGraphAppearance(list,this.filters.timeSerie.getName(),this.filters.name[j],style,errorbars));
      }
      this.minX = minX-86400000; 
      this.maxX = maxX+86400000;
       if(minY!= undefined){
        this.minY = minY.toFixed();
      }else{
        this.minY = minY;
      }
      if(maxY != undefined && minY != undefined){
        maxY = maxY*1.1;
        minY = minY*0.9;
        this.ticks = GraphHelper.generateTick(minY,maxY);
        this.minY = this.ticks[0];
      }
      this.timeRange.set({
        'startTime': this.minX,
        'endTime': this.maxX,
      });
      // this.timeRange.trigger('change');
      this.data = data;
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