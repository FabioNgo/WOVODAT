define(['require', 'views/series_tooltip', 'text!templates/tooltip_serie.html'],
    function (require) {
        'use strict';
        var
            $ = require('jquery'),
            Backbone = require('backbone'),
            _ = require('underscore'),
            // flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection', 'jquery.flot.errorbars', 'jquery.flot.axislabels','jquery.flot.legendoncanvas']),

            serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
            // template = require('text!templates/time_serie_graph.html'),
            Tooltip = require('views/series_tooltip'),
            TimeRange = require('models/time_range'),
            GraphHelper = require('helper/graph');
        // materialize = require('material');

        return Backbone.View.extend({
            initialize: function (options) {
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

                this.id = this.filters.timeSerie.get("sr_id")+"."+this.filters.filterAttributes[0].name;
                this.$el.attr('id',this.id);
                this.$el.html('<div id="allowErrorBar.'+this.id+'" style="padding-left: 50px">  </div> <div id="graph.'+this.id+'"> </div>');
                this.allowErrorBar = true;
                this.prepareData();
            },

            timeRangeChanged: function (TimeRange) {
                if (TimeRange == undefined) {
                    return;
                }
                this.minX = TimeRange.get('minX');
                this.maxX = TimeRange.get('maxX');
                this.overviewGraphMinX = TimeRange.get('overviewGraphMinX');
                this.overviewGraphMaxX = TimeRange.get('overviewGraphMaxX');
                // this.render();
                //console.log(this.filters);
                // put this new time range into filter as attributes.
                //this.prepareData();
            },

            onScroll: function (event, minX, maxX) {
                console.log(event.data);
            },

            onHover: function (event, pos, item) {
                // if(item!=null){
                var tooltip = event.data;
                tooltip.update(pos, item);
                // }

            },
            show: function () {

                //this.timeRangeChanged(this.timeRange);
                this.render();
            },
            showCheckbox : function(){
                //checkbox
                var checkboxRegion =$('[id="allowErrorBar.'+this.id+'"]');
                checkboxRegion.html('<form action="#"> <input type="checkbox" id="checkbox.'+this.id+'" checked="'+ String(!this.allowErrorBar)+'" class="filled-in"/> <label for="checkbox.'+this.id+'">Disable error bar</label> </form>');
                var checkbox = $('[id="checkbox.'+this.id+'"]');
                var self = this;
                // checkbox.
                checkbox[0].checked = !this.allowErrorBar;
                checkbox.change(self,function(e){
                    self.allowErrorBar = !this.checked;
                    var oldMinX = self.minX;
                    var oldMaxX = self.maxX;
                    self.prepareData();
                    self.minX = oldMinX;
                    self.maxX = oldMaxX;
                    self.render();
                });
            },
            render: function () {

                var options;
                if (this.data == undefined) {
                    return;
                }

                this.showCheckbox();
                var unit = undefined;
                for (var i = 0; i < this.data.length; i++) {
                    if (this.data[i].yaxis.axisLabel != undefined) {
                        unit = this.data[i].yaxis.axisLabel;
                    }
                }
                ;

                // change yaxix of timeseriesgraph according to zoomed in data

                var zoomedDataMinY = undefined;
                var zoomedDataMaxY = undefined;
                for (var j = 0; j < this.data.length; j++) {
                    for (var k = 0; k < this.data[j].data.length; k++) {
                        var currentData = this.data[j].data[k];
                        var previousData = this.data[j].data[k - 1];
                        if (this.data[j].points.show) {
                            if (currentData[2] == undefined) {
                                currentData[2] = 0;
                            }
                            if (currentData[0] >= this.minX && currentData[0] <= this.maxX) {
                                if (zoomedDataMinY == undefined) {
                                    zoomedDataMinY = currentData[1] - currentData[2];
                                }
                                else if (currentData[1] - currentData[2] < zoomedDataMinY) {
                                    zoomedDataMinY = currentData[1] - currentData[2];
                                }
                                ;
                            }

                            if (currentData[0] <= this.maxX && currentData[0] >= this.minX) {
                                if (zoomedDataMaxY == undefined) {
                                    zoomedDataMaxY = currentData[1] + currentData[2];
                                }
                                else if (currentData[1] + currentData[2] > zoomedDataMaxY) {
                                    zoomedDataMaxY = currentData[1] + currentData[2];
                                }
                                ;
                            }
                        }
                        else if (this.data[j].bars.show) {
                            if (currentData[4] == undefined) {
                                currentData[4] = 0;
                            }
                            if (currentData[0] >= this.minX && currentData[1] <= this.maxX) {
                                if (zoomedDataMinY == undefined) {
                                    zoomedDataMinY = currentData[2] - currentData[4];
                                }
                                else if ((currentData[2] - currentData[4]) < zoomedDataMinY) {
                                    zoomedDataMinY = currentData[2] - currentData[4];
                                }
                                ;
                            }

                            if (currentData[1] <= this.maxX && currentData[0] >= this.minX) {
                                if (zoomedDataMaxY == undefined) {
                                    zoomedDataMaxY = currentData[3] + currentData[4];
                                }
                                else if ((currentData[3] + currentData[4]) > zoomedDataMaxY) {
                                    zoomedDataMaxY = currentData[3] + currentData[4];
                                }
                                ;
                            }
                        }
                    }
                }
                ;
                this.ticks = GraphHelper.generateTick(zoomedDataMinY, zoomedDataMaxY)
                this.minY = this.ticks[0];
                this.maxY = this.ticks[this.ticks.length-1];
                // if(zoomedDataMaxY>=0&&zoomedDataMinY>=0){
                //
                //   this.minY = zoomedDataMinY*0.95;
                //   this.maxY = zoomedDataMaxY*1.05;
                // }
                // else if(zoomedDataMaxY<=0&&zoomedDataMinY<=0){
                //   this.minY = zoomedDataMinY*1.05;
                //   this.maxY = zoomedDataMaxY*0.95;
                // }
                // else if(zoomedDataMaxY>=0&&zoomedDataMinY<=0){
                //   this.minY = zoomedDataMinY*1.05;
                //   this.maxY = zoomedDataMaxY*1.05;
                // }

                options = {
                    grid: {
                        hoverable: true,
                    },
                    xaxis: {
                        mode: 'time',
                        timeformat: "%d-%b<br>%Y",
                        min: this.minX,
                        max: this.maxX,
                        autoscale: true,
                        canvas: true,
                        ticks: 6,
                        zoomRange: [30000000],
                    },
                    yaxis: {
                        show: true,
                        min: this.minY,
                        max: this.maxY,
                        ticks: this.ticks,
                        labelWidth: 60,
                        tickFormatter: function (val, axis) {
                            var string = val.toString();
                            if (string.length > 7) {
                                return val.toPrecision(2);
                            }
                            return val;
                        },
                        zoomRange: false,
                        axisLabel: unit,
                        canvas: true,
                    },
                    zoom: {
                        interactive: true,
                    },
                    // pan: {
                    //   interactive: true,
                    // },
                    tooltip: {
                        show: true,
                    },

                };
                if (!this.data || !this.data.length) {
                    this.$el.html('');
                    return;
                }
                // console.log(this.data);
                this.$el.width('auto');
                this.$el.height('auto');
                var graphHolder = $('[id="graph.'+this.id+'"]');
                graphHolder.height('200');
                graphHolder.width('auto');
                this.$el.addClass('time-serie-graph');
                // plot the time series graph after being selected (eg. onSelect in OverViewGraph).
                // config graph theme colors
                options.colors = ["#000000", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"];
                //console.log(this.data);
                var temp = this.$el.children();
                // console.log(this.minX+" "+this.maxX);
                this.graph = $.plot(graphHolder, this.data, options);
                this.$el.bind('plothover', this.tooltip, this.onHover);
                var eventData = {
                    startTime: this.minX,
                    endTime: this.maxX,
                    overviewGraphMinX: this.overviewGraphMinX,
                    overviewGraphMaxX: this.overviewGraphMaxX,
                    data: this.data,
                    graph: this.graph,
                    el: this.$el,
                    self: this,
                    original_option: options,
                    timeRange: this.serieGraphTimeRange
                }
                // if(!this.zoomBounded){
                this.$el.unbind('plotzoom');
                this.$el.bind('plotzoom', eventData, this.onZoom);
                this.zoomBounded = true;
                // }


            },
            onZoom: function (event, plot) {
                var xaxis = plot.getXAxes()[0];
                /* The zooming range cannot wider than the original range */
                if (xaxis.min < event.data.overviewGraphMinX || xaxis.max > event.data.overviewGraphMaxX) {
                    xaxis.min = event.data.overviewGraphMinX;
                    xaxis.max = event.data.overviewGraphMaxX;
                } else {
                }
                event.data.timeRange.set({
                    minX: xaxis.min,
                    maxX: xaxis.max
                })
                event.data.timeRange.trigger('zoom');
                event.data.timeRange.trigger('update');
            },
            prepareData: function () {
                if (this.filters == undefined) {
                    this.data = undefined;
                    return;
                }
                var filters = [this.filters];
                var allowErrorbar = this.allowErrorBar;
                var allowAxisLabel = true;
                var limitNumberOfData = false;
                GraphHelper.formatData(this, filters, allowErrorbar, allowAxisLabel, limitNumberOfData);
            }
        });
    });
