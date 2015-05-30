define(['jquery', 'backbone', 'helper/date'], function($, Backbone, DateHelper) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'sr_id',

    initialize: function() {
      this.url = 'api/?data=time_serie&sr_id=' + this.get("sr_id");
    },
    getDisplayName : function() {

    	var x =  this.get("data_type")  
          + ( this.has('component') ? '-' + this.get('component') : ""  )
          + ( this.has('station_code') ?  " (" + this.get('station_code') + ")" : ""  );
      //console.log(x);
      //console.log( this.has('station_code') );

      return x;
    },
    prepareDataForGraph : function(filter) {
      if ( this.get("data_type") == "Evn" && this.get("component") == "Earthquake" ) 
        return this.prepareDataForEarthquake(filter);
      return this.prepareDataForNormalCase(filter);
    },
    prepareDataForNormalCase : function(filter) {
      var minValue = 0;
      var maxValue = 0;
      var bars = false;
      var lines = false;
      var points = false;
      var data = this.get("data");
      var a = [];
      var selectedFilter = filter.get("filter");

      if (data[0] && data[0].stime)
        bars = true;
      if (data[0] && data[0].time)
        lines = true;

      // special for sd_evn
      if ( this.get("data_type") == "Evn" ) {
        lines = false;
        bars = false;
        points = true;
      }

      data.forEach(function(ds) {
        if ( (!selectedFilter)  || _.isEqual(ds.filter, selectedFilter)) {
          if (ds.stime)
            a.push([ds.stime, ds.value, 0, ds.etime - ds.stime, ds]);
          else 
            a.push([ds.time, ds.value, 0, 0 , ds ]);
          maxValue = Math.max(maxValue, ds.value);
          minValue = Math.min(minValue, ds.value);
        }
      });

      return {
        data : a,
        bars : bars,
        lines : lines,
        points : points, 
        maxValue : maxValue,
        minValue : minValue
      };
    },

    prepareDataForEarthquake : function(filter) {
      var minValue = 0;
      var maxValue = 0;
      var bars = true;
      var lines = false;
      var points = false;
      var data = this.get("data");
      var a = {};
      var selectedFilter = filter.get("filter");

      data.forEach(function(ds) {
        if ( (!selectedFilter)  || _.isEqual(ds.filter, selectedFilter)) {
          var date = DateHelper.formatDate(ds.time);
          if ( a[date] ) a[date]++;
          else a[date] = 1;
        }
      });


      var b = [];
      for(var property in a ) {
        var val = a[property];
        maxValue = Math.max( maxValue, val );
        minValue = Math.min( minValue, val );
       
        var milliseconds = DateHelper.dateToMillisecond(property);

        b.push([ milliseconds, val, 0, 1000 * 60 * 60 * 24, 
          {   time : milliseconds,
              value : val
               } ]);
      }

      return {
        data : b,
        bars : bars,
        lines : lines,
        points : points, 
        maxValue : maxValue,
        minValue : minValue
      };

    }


  });
});