define(function (require) {
    'use strict';
    var
        $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        // flot = require(['jquery.flot', 'jquery.flot.time', 'jquery.flot.navigate', 'jquery.flot.selection', 'jquery.flot.errorbars', 'jquery.flot.axislabels','jquery.flot.legendoncanvas']),
        JSZip = require('jszip'),
        FileSaver = require('FileSaver'),
        serieTooltipTemplate = require('text!templates/tooltip_serie.html'),
        template = require('text!templates/time_serie_graph.html'),
        Tooltip = require('views/series_tooltip'),
        TimeRange = require('models/time_range'),
        Handlebars = require('handlebars'),
        GraphHelper = require('helper/graph');
    // materialize = require('material');

    return Backbone.View.extend({
        // template: _.template(template),
        events: {
            'change input': 'onChange',
            'click a': 'onClick'
        },
        initialize: function (options) {
            this.filters = options.filters;
            this.selectedVolcano = options.selectedVolcano;
            this.eruptionTimeRange = options.eruptionTimeRange;
            this.serieGraphTimeRange = options.serieGraphTimeRange;
            this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;

            this.timeRange = new TimeRange();
            // console.log(Tooltip);
            this.tooltip = new Tooltip({
                template: serieTooltipTemplate
            });
            // console.log(this.serieGraphTimeRange);

            this.id = this.filters.timeSerie.get("sr_id") + "." + this.filters.filterAttributes[0].name;
            this.$el.attr('id', this.id);
            this.hasErrorBar = this.filters.timeSerie.get('data').errorbar;
            // this.owner = this.filters.timeSerie.get('data').
            var preHtml = Handlebars.compile(template);
            var options = {
                id: this.id,
                hasErrorBar: this.hasErrorBar,
                owner: ""
            };

            this.allowErrorBar = true;
            this.token = "";
            this.prepareData();
            if(this.filters.filterAttributes[0].dataOwner != undefined){
                this.owner = this.filters.filterAttributes[0].dataOwner[0];
            }
            options.owner = this.owner;
            var html = preHtml(options);
            this.$el.html(html);
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
        onChange: function (e) {
            if (e.currentTarget.id == ("checkbox." + this.id)) {
                this.allowErrorBar = !e.currentTarget.checked;
                var oldMinX = this.minX;
                var oldMaxX = this.maxX;
                this.prepareData();
                this.minX = oldMinX;
                this.maxX = oldMaxX;
                this.render();
            }

        },
        onClick: function (e) {
            if (e.currentTarget.id == ("csv." + this.id)) {
                this.popUpInfoForm();
            }
            if (e.currentTarget.id == "submit-form." + this.id) {
                this.submitDownloadForm()
            }
        },
        showFunctions: function () {
            //checkbox
            var container = $('[id="graph-container.' + this.id + '"]');
            container.css({display: "block"});
            var checkbox = $('[id="checkbox.' + this.id + '"]');
            var self = this;
            // checkbox.
            if (checkbox[0] != undefined) {
                checkbox[0].checked = !this.allowErrorBar;
            }


        },
        render: function () {

            var options;
            if (this.data == undefined) {
                return;
            }

            this.showFunctions();
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
            this.maxY = this.ticks[this.ticks.length - 1];

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
            var graphHolder = $('[id="graph.' + this.id + '"]');
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
        },
        getDataForSendingEmail: function (URL, name, email, institution) {
            var dataType = [];
            var startTimeList = [];
            var endTimeList = [];
            var volcanoName = this.filters.timeSerie.attributes.volcanoName;
            var startDateTime = 0;
            var endDateTime = 0;
            var filterName = this.filters.filterAttributes[0].name;
            var monitoringData = this.filters.timeSerie.attributes.component + " (" + filterName + ")";
            dataType.push(monitoringData);
            var data = this.filters.timeSerie.attributes.data.data;
            for (var p = 0; p < data.length; p++) {

                if (data[p].filter != filterName) continue;
                var data = this.filters.timeSerie.attributes.data.data;
                var stime =  data[p].time;
                var etime = 0;
                if (stime == undefined) {
                    stime = data[p].stime;
                    etime = data[p].etime;
                }
                if (stime >= this.serieGraphTimeRange.attributes.startTime && stime <= this.serieGraphTimeRange.attributes.endTime){
                    if (startDateTime == 0){
                        startDateTime = stime;
                    }
                    endDateTime = stime
                    if (etime != 0 ){
                        endDateTime = etime;
                    }
                }

            }
            startTimeList.push(startDateTime);
            endTimeList.push(endDateTime);


            var volcanoName = this.filters.timeSerie.attributes.volcanoName;
            //var dataTypeStr = dataType.join(",");
            this.generateCSV();
            var dataDownload = {
                data: "add_user",
                name: name,
                email: email,
                institution: institution,
                vd_name: volcanoName,
                dataType: dataType,
                startTimeStr: startTimeList,
                endTimeStr: endTimeList
            }
            $.get(URL, dataDownload);
        },
        /**
         *Generate CSV file when click CSV button
         volcano-name (vd_name),
         station/seismic network name (ds/ss/sn_name),
         date-time (dd_tlt_time),
         code of data (dd_tlt_code, sd_ivl_code, etc.),
         data (dd_tlt1),
         data-uncertainty (dd_tlt_err1),
         data owner (cc_code).
         */
        generateCSV: function () {
            var content = [];
            var stationName = this.filters.timeSerie.attributes.station_code1;
            var volcanoName = this.filters.timeSerie.attributes.volcanoName;
            var showingName = this.data[0].label;
            var filterName = this.filters.filterAttributes[0].name;
            var network = this.filters.timeSerie.attributes.short_data_type;
            var monitoringData = this.filters.timeSerie.attributes.component + " (" + filterName + ")";


            var data = this.filters.timeSerie.attributes.data.data;
            for (var p = 0; p < data.length; p++) {
                if (data[p].filter != filterName) continue;
                var startTimeStr;
                var endTimeStr = "";
                var startTime
                var data = this.filters.timeSerie.attributes.data.data;
                var stime = data[p].time;
                var etime = 0;
                if (stime == undefined) {
                    stime = data[p].stime;
                    etime = data[p].etime;
                }

                var startDateTime = new Date(stime);
                var startTimeStr = startDateTime.getDate() + "-" + (startDateTime.getMonth() + 1) + "-" + startDateTime.getFullYear() + " " + startDateTime.getHours() + ":" + startDateTime.getMinutes() + ":" + startDateTime.getSeconds();
                endTimeStr= startTimeStr;
                if (etime != 0) {
                    var endDateTime = new Date(etime);
                    endTimeStr = endDateTime.getDate() + "-" + (endDateTime.getMonth() + 1) + "-" + endDateTime.getFullYear() + " " + endDateTime.getHours() + ":" + endDateTime.getMinutes() + ":" + endDateTime.getSeconds();
                }

                var value = data[p].value;
                var dataOwner = [];
                for (var i = 0; i < data[p].data_owner.length; i = i + 2) {
                    dataOwner.push(data[p].data_owner[i]);
                }
                var dataOwner = dataOwner.join(",");
                var uncertainty = data[p].error;
                if (uncertainty == undefined) uncertainty = "";
                if (stime >= this.serieGraphTimeRange.attributes.startTime && stime <= this.serieGraphTimeRange.attributes.endTime) {
                    //console.log (value);
                    var d = {
                        volcano: volcanoName,
                        network: network,
                        station: stationName,
                        monitoringData: monitoringData,
                        data: value,
                        startTime: startTimeStr,
                        endTime: endTimeStr,
                        uncertainty: uncertainty,
                        dataOwner: dataOwner,
                        showingName: showingName
                    }
                    content.push(d);
                }

            }

            if (this.data == undefined) return;

            var headers = ['Volcano', 'Network', 'Station', 'Monitoring Data (Type)', 'Data', 'Start Time', 'End Time',
                'Data Uncertainty', 'Data Owner'];
            //var z = new Zip();
            //console.log(z);
            var zip = new JSZip();
            // for (var i = 0 ; i < listContent.length; i++){
            var csvContent = "data:text/csv;charset=utf-8,";
            // for (var ii = 0; ii < listContent.length; ii++) {
            //     var content = listContent[ii];
            var total = 0;

            // var content = listContent[i];
            // if (content == undefined) continue;
            var dataString = "";
            for (var p = 0; p < content.length; p++) {
                total++;
                var d = content[p];
                dataString += d.volcano + ",\"" + d.network + "\",\"" + d.station + "\",\"" + d.monitoringData + " \",\"" + d.data + "\",\""
                    + d.startTime + "\",\"" + d.endTime + " \",\"" + d.uncertainty + " \",\"" + d.dataOwner + " \"\n";
            }

            csvContent += "Total number of earthquakes: " + total + " \n";
            csvContent += "(100 km from volcanic vent)\n";
            csvContent += headers.join(",") + "\n";
            csvContent += dataString + "\n";
            var filename = "";
            if (content.length != 0) {
                filename = content[0].showingName;
            } else {
                filename = "Blank"
            }
            zip.file(filename + ".csv", csvContent);
            // }

            //}
            zip.generateAsync({type: "blob"})
                .then(function (blob) {
                    saveAs(blob, "data.zip");
                });


        },
        /**
         * Display a pop up to make user fill in their information
         * If user have been keyed in information, just donwload, no popup
         */
        popUpInfoForm: function () {
            var token = this.token;

            var dataToken = {
                data: "check_token",
                token: token,
            }
            var URL = "/eruption/api/";
            var tokenExists;
            var self = this;
            $.get(URL, dataToken, function (data, status, xhr) {
                tokenExists = data;
                if (tokenExists) {
                    self.getDataForSendingEmail(URL, "", "", "");

                } else {
                    $('[id="formPopup.' + self.id + '"]').openModal();

                }
            }, "json")

        },
        submitDownloadForm: function (e) {
            // var a= this.$el.find('#name');
            var name = this.$el.find('#name')[0].value.trim();
            var email = this.$el.find('#email')[0].value.trim();
            var institution = this.$el.find('#institution')[0].value.trim();
            var filterName = this.filters.filterAttributes[0].name;
            var volcanoName = this.filters.timeSerie.attributes.volcanoName;
            var dataType = this.filters.timeSerie.attributes.component + " (" + filterName + ")";
            var agreeTerm = this.$el.find("#agree-term")[0].checked;
            var atpos = email.indexOf("@");
            var dotpos = email.lastIndexOf(".");
            if (name === "") {
                return false;
            }
            if (institution === "") {
                return false;
            }
            if (email == "") {
                return false;
            }
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.length) {
                return false;
            }
            if (!agreeTerm) {
                return false;
            }

            var dataToken = {
                data: "gen_token",
                name: name,
                email: email,
            }
            var URL = "/eruption/api/";
            var a = this;
            $.getJSON(URL, dataToken, function (data) {
                a.token = data.token;
            });
            this.getDataForSendingEmail(URL, name, email, institution);
            $('[id="formPopup.' + this.id + '"]').closeModal();
            return false;

            //document.getElementById("download").appendChild(input);
            //document.getElementById("download").submit();

        },
    });
});
