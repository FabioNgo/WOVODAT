define(function (require) {
    'use strict';
    var $ = require('jquery'),
        Backbone = require('backbone'),
        _ = require('underscore'),
        Filter = require('models/filter'),
        Filters = require('collections/filters'),
        template = require('text!templates/filter.html'),
        loading = require('text!templates/loading.html'),
        Handlebars = require('handlebars'),
        materialize = require('material');

    return Backbone.View.extend({
        el: '',

        className: "mgt15",
        initialize: function (options) {

            this.observer = options.observer;
            this.selectingTimeSeries = options.selectingTimeSeries;
            this.selectingFilters = options.selectingFilters;
            this.categories = options.categories;
            this.selectedFilter = options.selectedFilters;
            this.filters = new Filters;
        },
        selectingTimeSeriesChanged: function (selectingTimeSeries) {
            this.selectingTimeSeries = selectingTimeSeries;
            // this.filters.reset();

            this.render(this.filters);


        },
        //this.filter is grouped by timeSerie and category
        getFilter: function (timeSerie) {
            var data = timeSerie.attributes.data.data;
            if (data == undefined) {
                return;
            }
            this.filters.empty = false;
            if (data.length == 0) {
                this.filters.push(timeSerie, "  "); //no data
            }
            for (var i = 0; i < data.length; i++) {
                this.filters.push(timeSerie, data[i].filter);
            }
        },

        updateSelectingFilters: function () {
            /* remove timeseries which are no longer selected*/
            var filters = [];
            var categories = this.categories;
            for (var i = 0; i < categories.length; i++) {
                if (this.selectingFilters[categories[i]] != undefined) {
                    filters = filters.concat(this.selectingFilters[categories[i]]);
                }
            }
            for (var i = 0; i < filters.length; i++) {
                var pos = -1;
                for (var j = 0; j < this.selectingTimeSeries.length; j++) {

                    if (this.selectingTimeSeries.models[j].get('sr_id') == filters[i].timeSerie.get('sr_id')) {
                        pos = j;
                        break;
                    }

                }
                if (pos == -1) {
                    this.selectingFilters.removeFilter(filters[i]);
                }
            }
            //add timeseries have no filter
            for (var i = 0; i < categories.length; i++) {
                if (this.filters[categories[i]] != undefined) {
                    var groupedFilters = this.filters[categories[i]];
                    for (var j = 0; j < groupedFilters.length; j++) {
                        var filter = groupedFilters[j];
                        if (filter.filterAttributes[0].name == " ") {
                            this.selectingFilters.push(filter.timeSerie, " ");
                            this.selectingFilters.empty = false;
                        }
                    }

                }
            }
        },
        render: function (options) {
            this.filters.reset();

            /* get filter from selecting Time Series */
            var categories = this.categories;
            for (var i = 0; i < categories.length; i++) {
                delete this.filters[categories[i]];
            }
            this.filters.empty = true;
            var models = this.selectingTimeSeries.models;
            for (var i = 0; i < models.length; i++) {
                this.getFilter(models[i]);

            }
            ;

            // var categories=["Seismic","Deformation","Gas","Hydrology","Thermal","Field","Meteology"];
            // var selectingFilters = [];
            // for(var i = 0;i<categories.length;i++){
            //   selectingFilters = selectingFilters.concat(this.selectingFilters.getAllFilters(categories[i]));
            // }

            var temp = Handlebars.compile(template);
            Handlebars.registerHelper('list', function (items, options) {
                var ret = "";
                for (var i = 0, j = items.length; i < j; i++) {
                    ret = ret + options.fn(items[i]);
                }
                return ret;
            });
            Handlebars.registerHelper('if', function (condition, options) {
                if (condition) {
                    return options.fn(this);
                } else {
                    return options.inverse(this);
                }
            });
            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];
                var options = {
                    series: this.generateData(category)
                }
                var html = temp(options);
                $('.filter-field' + '.' + category).html(html);
            }


            $('.filter-select').material_select();
            this.showGraph();
            this.selectedFilter = undefined; // selectedFIlter only valid at the first time of loading
        },
        //generate data for html template
        /* {[{nodata,
         showingName,
         filter:[{isSelected,value,showingName}]
         }]} */
        generateData: function (category) {
            var output = [];
            var nodata = true;
            if (this.filters[category] == undefined) {
                return output;
            }
            for (var i = 0; i < this.filters[category].length; i++) {
                var groupFilters = this.filters[category][i];
                var serie = {}
                serie.name = groupFilters.timeSerie.attributes.showingName;
                if (groupFilters.filterAttributes[0].name == "  ") {
                    serie.nodata = true;
                } else {
                    serie.nodata = false;
                }

                if (groupFilters.filterAttributes[0].name != "  ") {
                    serie.filters = [];
                    for (var k = 0; k < groupFilters.filterAttributes.length; k++) {
                        var filter = groupFilters.filterAttributes[k].name;
                        if (filter == " ") {
                            serie.hasfilter = false;
                            break;
                        }
                        serie.hasfilter = true;
                        var object = {
                            value: groupFilters.timeSerie.get('sr_id') + "." + filter,
                            showingName: filter,
                        }
                        object.isSelected = this.isSelected(groupFilters.timeSerie, filter);
                        serie.filters.push(object);
                    }
                }
                output.push(serie);
            }
            return output;
        },
        hide: function () {
            this.$el.html("");

            var categories = this.categories;
            for (var i = 0; i < categories.length; i++) {
                delete this.selectingFilters[categories[i]];
            }
            this.selectingFilters.empty = true;
            // this.render();
            this.trigger('hide');

        },

        showGraph: function (event) {


            var categories = this.categories;
            for (var i = 0; i < categories.length; i++) {
                delete this.selectingFilters[categories[i]];
            }
            this.selectingFilters.empty = true;
            var checkboxes = $('.filter-select-option');
            // for(var i = 0; i<selects.length;i++){
            for (var i = 0; i < checkboxes.length; i++) {
                var checkbox = checkboxes[i];
                if (checkbox.id.split(".")[1] == this.selectedFilter) {
                    checkbox.checked = true;
                }
                if (checkbox.checked) {
                    var temp = checkbox.id.split(".");
                    this.selectingFilters.empty = false;
                    this.selectingFilters.push(this.selectingTimeSeries.get({sr_id: temp[0]}), temp[1]);
                }

            }

            this.updateSelectingFilters();
            this.selectingFilters.trigger('update');


        },
        isSelected: function (timeSerie, filterName) {
            var selectingFilters = this.selectingFilters[timeSerie.get('category')];
            if (selectingFilters == undefined) {
                return false;
            }
            for (var i = 0; i < selectingFilters.length; i++) {

                var model = selectingFilters[i];
                if (timeSerie.get('sr_id') == model.timeSerie.get('sr_id')) {
                    for (var j = 0; j < model.filterAttributes.length; j++) {
                        if (filterName == model.filterAttributes[j].name) {
                            return true;
                        }
                    }
                }
            }
            return false;
        },
        destroy: function () {
            // From StackOverflow with love.
            this.undelegateEvents();
            this.$el.removeData().unbind();
            this.remove();
            Backbone.View.prototype.remove.call(this);
        }
    });
});