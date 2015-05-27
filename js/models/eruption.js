define(['jquery', 'backbone', 'helper/date'], function($, Backbone, DateHelper) {
  'use strict';

  return Backbone.Model.extend({
    idAttribute: 'ed_id',
    
    initialize: function() {
      var ed_stime = parseInt(this.get('ed_stime'), 10),
          ed_etime = parseInt(this.get('ed_etime'), 10) || new Date().getTime(),
          ed_phs = this.get('ed_phs') || [];
      this.set('ed_stime', ed_stime);
      this.set('formatted_ed_stime', DateHelper.formatDate(ed_stime));
      this.set('ed_etime', ed_etime);
      this.set('formatted_ed_etime', DateHelper.formatDate(ed_stime));
      ed_phs.forEach(function(e, i) {
        ed_phs[i].ed_phs_stime = parseInt(e.ed_phs_stime, 10);
        ed_phs[i].formatted_ed_phs_stime = DateHelper.formatDate(e.ed_phs_stime);
        ed_phs[i].ed_phs_etime = parseInt(e.ed_phs_etime, 10) || new Date().getTime();
        ed_phs[i].formatted_ed_phs_etime = DateHelper.formatDate(e.ed_phs_etime);
      });
    },

    getDate: function() {
      var stime = this.get('ed_stime');
      return DateHelper.formatDate(stime);
    }
  });
});