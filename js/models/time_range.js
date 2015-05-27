define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'ed_id',
    
    initialize: function(options) {
      this.set({
        'startTime': options ? options.startTime : undefined,
        'endTime': options ? options.endTime : undefined,
      });
    }
  });
});