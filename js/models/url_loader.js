define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      UrlHelper = require('helper/url'),
      moment = require('moment');

  return Backbone.View.extend({
    initialize: function(options) {
      _(this).bindAll('selectVolcano', 'selectEruptionByStartTime');
      
      this.observer = options.observer;
      this.volcanoes = options.volcanoes;
      this.selectingVolcano = options.selectingVolcano;
      this.eruptions = options.eruptions;
      this.selectingEruption = options.selectingEruption;

      this.listenToOnce(this.volcanoes, 'sync', this.selectVolcano);
      this.listenToOnce(this.eruptions, 'sync', this.selectEruptionByStartTime);

    },

    selectVolcano: function() {
      var vd_id = UrlHelper.getParam('vd_id');
      if (vd_id && this.volcanoes.get(vd_id))
        this.selectingVolcano.set('vd_id', vd_id);
    },

    selectEruptionByStartTime: function() {
      var vd_id = UrlHelper.getParam('vd_id'),
          startTime = UrlHelper.getParam('start_time'),
          ed_id = undefined;

      if (vd_id && startTime) {
        startTime = moment.utc(startTime, 'YYYY-MM-DDTHH:mm:ss').format('X');

        this.eruptions.models.forEach(function(ed) {
          if (ed.get('ed_stime') / 1000 == startTime) {
            ed_id = ed.get('ed_id');
          }
        });
        if (ed_id) {
          this.selectingEruption.set('ed_id', ed_id);
        }
      }
    }
  });
});