define(['jquery', 'backbone'], function($, Backbone) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'filter',
    
    initialize: function() {
    }
  });
});