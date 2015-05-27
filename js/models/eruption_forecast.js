define(['jquery', 'backbone', 'helper/date'], function($, Backbone, DateHelper) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'ed_id',
    
    initialize: function() {
        this.set('ed_for_astime', parseInt(this.get('ed_for_astime'), 10));
        this.set('formatted_ed_for_astime', DateHelper.formatDate(this.get('ed_for_astime')));
        this.set('ed_for_aetime', parseInt(this.get('ed_for_aetime'), 10) || new Date().getTime());
        this.set('formatted_ed_for_aetime', DateHelper.formatDate(this.get('ed_for_aetime')));
    }
  });
});