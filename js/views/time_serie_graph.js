define(['require','views/series_tooltip','text!templates/tooltip_serie.html'],
  function(require) {
  'use strict';
  var 
  $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      // flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection', 'jquery.flot.errorbars', 'jquery.flot.axislabels','jquery.flot.legendoncanvas']),

      serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
      Tooltip = require('views/series_tooltip'),
      TimeRange = require('models/time_range'),
      GraphHelper = require('helper/graph');
       // materialize = require('material');

  return Backbone.View.extend({    
    initialize: function(options) {
      this.filters = options.filters;
      this.eruptionTimeRange = options.eruptionTimeRange;
      this.serieGraphTimeRange = options.serieGraphTimeRange;
      this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
      this.timeRange = new TimeRange();
      // console.log(Tooltip);
      this.tooltip = new Tooltip({
        template: serieTooltipTemplate
      });
      // console.log(this.serieGraphTimeRange);
      this.prepareData();
    },

    timeRangeChanged: function(TimeRange){
      if(TimeRange == undefined){
        return;
      }
      this.minX = TimeRange.get('startTime');
      this.maxX = TimeRange.get('endTime');
      //this.render();
      //console.log(this.filters);
      // put this new time range into filter as attributes. 
      //this.prepareData();
    },

    onHover: function(event, pos, item) {
      // if(item!=null){
        var tooltip = event.data;
      tooltip.update(pos, item);  
      // }
      
    },
    show: function(){
      
      //this.timeRangeChanged(this.timeRange);
      this.render();
    },
    render: function() {
      if(this.data==undefined){
        return;
      }
      this.$el.html("");
      var unit = undefined;
      for(var i=0;i<this.data.length;i++){
        if(this.data[i].yaxis.axisLabel != undefined){
          unit = this.data[i].yaxis.axisLabel;
        }
      };
      // change yaxix according to zoomed in data
      // PROBLEM FOR Fluctuate Data maxY or minY may not be MaxX or MinX they can lie in between
      var zoomedDataMinY = undefined;
      var zoomedDataMaxY = undefined;
      for(var j=0;j<this.data.length;j++){
        for(var k=0;k<this.data[j].data.length;k++){
                var currentData = this.data[j].data[k];
                var previousData = this.data[j].data[k-1];
                if(this.data[j].points.show){
                  if(currentData[0]>=this.minX&&currentData[0]<=this.maxX){
                    if(zoomedDataMinY == undefined){
                      zoomedDataMinY = currentData[1];
                    }
                    else if(currentData[1]<zoomedDataMinY){
                      zoomedDataMinY = currentData[1];
                    };
                  }

                  if(currentData[0]<=this.maxX&&currentData[0]>=this.minX){
                    if(zoomedDataMaxY == undefined){
                      zoomedDataMaxY = currentData[1];
                    }
                    else if(currentData[1]>zoomedDataMaxY){
                      zoomedDataMaxY = currentData[1];
                    };
                  }
                }
                else if(this.data[j].bars.show){
                  if(currentData[0]>=this.minX&&currentData[1]<=this.maxX){
                    if(zoomedDataMinY == undefined){
                      zoomedDataMinY = currentData[3]-currentData[4];
                    }
                    else if((currentData[3]-currentData[4])<zoomedDataMinY){
                      zoomedDataMinY = currentData[3]-currentData[4];
                    };
                  }

                  if(currentData[1]<=this.maxX&&currentData[0]>=this.minX){
                    if(zoomedDataMaxY == undefined){
                      zoomedDataMaxY = currentData[2]+currentData[4];
                    }
                    else if((currentData[2]+currentData[4])>zoomedDataMaxY){
                      zoomedDataMaxY = currentData[2]+currentData[4];
                    };
                  }
                }
        }
      };
      if(zoomedDataMaxY>=0&&zoomedDataMinY>=0){
        this.minY = zoomedDataMinY*0.95;
        this.maxY = zoomedDataMaxY*1.05;
      }
      else if(zoomedDataMaxY<0&&zoomedDataMinY<0){
        this.minY = zoomedDataMinY*1.05;
        this.maxY = zoomedDataMaxY*0.95;
      }
      else if(zoomedDataMaxY>0&&zoomedDataMinY<0){
        this.minY = zoomedDataMinY*1.05;
        this.maxY = zoomedDataMaxY*1.05;
      }

      var options = {
            grid:{
              margin: 50,
            },
            xaxis: { 
              mode:'time',
              timeformat: "%d-%b<br>%Y",
              min: this.minX,
              max: this.maxX,

              autoscale: true,
              canvas: true,
              ticks: 6
            },
            yaxis: {
              show: true,
              min: this.minY,
              max: this.maxY,
              ticks: 6, //this.ticks
              labelWidth: 60,
              tickFormatter: function (val, axis) {
                var string = val.toString();
                if(string.length >7){
                  return val.toPrecision(2);
                }
                return val;
              },
              zoomRange: false,
              axisLabel: unit,
              canvas: true,
              autoscaleMargin: 5,
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
      this.$el.addClass('time-serie-graph card-panel');
      // plot the time series graph after being selected (eg. onSelect in OverViewGraph).
      // config graph theme colors
      options.colors = ["#000000", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"];
      //console.log(this.data);
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
      var filters = [this.filters];
      var allowErrorbar = true;
      var allowAxisLabel =true;
      var limitNumberOfData =false;
      //formatData: function(graph,filters,allowErrorbar,allowAxisLabel,limitNumberOfData)
      GraphHelper.formatData(this,filters,allowErrorbar,allowAxisLabel,limitNumberOfData); 
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