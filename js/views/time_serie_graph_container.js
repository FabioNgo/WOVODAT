define(function (require) {
    'use strict';
    var $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore');

    var TimeSerieGraph = require("views/time_serie_graph");

    return Backbone.View.extend({
        el: '',
        className: "time-series-graph-container",
        initialize: function (options) {
            this.selectingTimeSeries = options.selectingTimeSeries;
            this.serieGraphTimeRange = options.serieGraphTimeRange;
            this.eruptionTimeRange = options.eruptionTimeRange;
            this.forecastsGraphTimeRange = options.forecastsGraphTimeRange;
            this.filterObserver = options.filterObserver;
            // this.listenTo( this.selectingFilter, "add", this.addGraph );
            // this.listenTo( this.selectingFilter, "remove", this.removeGraph );
            this.categories = options.categories;
            this.beingShown = false;
            this.graphs = [];
            this.$el.append('<div class = "individual-graph-title" style = "font-weight: bold; color : black; background-color:white; padding-left: 50px; display: none">INDIVIDUAL GRAPHS');
        },

        // addGraph : function( filter ) {
        // 	var val = filter.get("filter");

        // 	this.graphs[val] = new TimeSerieGraph( {
        // 		timeRange : this.timeRange,
        // 		model : this.model,
        // 		filter : filter
        // 	});
        // 	this.$el.append(this.graphs[val].$el);

        // 	this.graphs[val].filter.trigger("change");

        // 	this.filterObserver.trigger("filter-change");
        // },
        addGraph: function (filters) {
            // var val = filter.get("filter");
            // selectingTimeSeries.
            // timeSerie.fetch({
            //   success: function(collection, response) {
            //     // console.log(e);
            //     console.log(response);

            //   }
            // });
            var timeSerieGraph = new TimeSerieGraph({
                // timeRange : this.timeRange,
                filters: filters,
                eruptionTimeRange: this.eruptionTimeRange,
                serieGraphTimeRange: this.serieGraphTimeRange,
                forecastsGraphTimeRange: this.forecastsGraphTimeRange,
            });
            this.$el.append(timeSerieGraph.$el);
            this.graphs.push(timeSerieGraph);
            // this.show();

            // this.graphs[val].filter.trigger("change");

            // this.filterObserver.trigger("filter-change");
        },
        serieGraphTimeRangeChanged: function (timeRange) {

            for (var i = 0; i < this.graphs.length; i++) {
                this.graphs[i].timeRangeChanged(timeRange);
            }
            this.show();

        },
        selectingFiltersChanged: function (selectingFilters) {
            this.graphs.length = 0;
            this.$el.html("");
            var filters = [];
            var categories = this.categories;
            for (var i = 0; i < categories.length; i++) {
                if (selectingFilters[categories[i]] != undefined) {
                    filters = filters.concat(selectingFilters.getAllFilters(categories[i]));
                }
            }
            for (var i = 0; i < filters.length; i++) {
                this.addGraph(filters[i]);
            }
            ;
        },
        // render: function(selectingTimeSeries) {
        //   this.overviewGraph.$el.appendTo(this.$el);
        // },
        hide: function () {
            this.$el.html("");
            this.$el.removeClass("card-panel");
            $('#individual-graph-title').css({display: "none"});
        },
        show: function () {
            // this.$el.html("");
            $('#individual-graph-title').css({display: "block"});
            this.$el.addClass("card-panel");
            for (var i = 0; i < this.graphs.length; i++) {

                this.graphs[i].show();

            }
            ;
        },

    });

});