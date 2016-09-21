define(function (require) {
    'use strict';
    var $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        // Serie = require('models/serie'),
        // TimeSerieGraph = require('views/time_serie_graph'),

        TimeRange = require('models/time_range'),
        Eruption = require('models/eruption'),
        Eruptions = require('collections/eruptions'),
        EruptionSelect = require('views/eruption_select');


    return Backbone.View.extend({
        el: '',
        className: "overview-graph-container",
        initialize: function (options) {
            /** Variable declaration **/
            this.overviewSelectingTimeRange = new TimeRange();
            this.observer = options.observer;
            this.overviewSelectingTimeSeries = options.selectingTimeSeries;
            this.overviewGraph = options.graph;
            this.$el.append("<div id = \"overview-title\" style = \"padding-left: 50px;display:none;\">" +
                "<a style = \" font-weight: bold; color : black;\">Overview Graph.</a> <br>" +
                "<a style = \" padding-left : 10px;color : black; \">Highlight selected time range using mouse</a></div>");
        },
        //hide overview graph from page
        hide: function () {
            this.$el.html("");
            this.$el.removeClass("card-panel");
            this.trigger('hide');
            $('#overview-title').css({display:"none"});
        },

        //show overview graph on page
        show: function () {
            $('#overview-title').css({display:"block"});
            this.render();
        },
        selectingFiltersChanged: function (selectingFilters) {
            this.selectingFilters = selectingFilters;
            if (this.selectingFilters.empty) {
                this.hide();
            } else {
                this.show();
            }
        },

        render: function () {
            this.$el.addClass("card-panel");

            this.overviewGraph.$el.appendTo(this.$el);
        }
    });
});