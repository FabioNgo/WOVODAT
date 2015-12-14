require.config({
  paths: {
    // Vendors.
    'jquery': '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min',
    'backbone': '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.2.3/backbone-min',
    'underscore': '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min',
    'text': '//cdnjs.cloudflare.com/ajax/libs/require-text/2.0.12/text.min',
    'moment': '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min',
    'pace': '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min',
    'material':'vendor/materialize/materialize',
    'hammer':'//cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.4/hammer',
    'velocity':'//cdnjs.cloudflare.com/ajax/libs/velocity/1.2.3/velocity.min',
    'jquery.flot': 'vendor/jquery-flot/jquery.flot.wovodat',
    'jquery.flot.navigate': 'vendor/jquery-flot/jquery.flot.navigate',
    'jquery.flot.selection': 'vendor/jquery-flot/jquery.flot.selection',
    'jquery.flot.time': 'vendor/jquery-flot/jquery.flot.time',
    'excanvas' : 'vendor/jquery-flot/excanvas',
    'jquery.flot.tickrotor': 'vendor/jquery-flot/jquery.flot.tickrotor',
    'jquery.flot.errorbars': 'vendor/jquery-flot/jquery.flot.errorbars',
    'jquery.flot.axislabels': 'vendor/jquery-flot/jquery.flot.axislabels',
    'jquery.flot.legendoncanvas' :'vendor/jquery-flot/jquery.flot.legendoncanvas',    
  },
  shim: {
    'material': {
      deps: ['jquery', 'hammer', 'velocity']
    },
    'jquery.flot.time': {
      deps: ['jquery.flot']
    },
    'jquery.flot.navigate': {
      deps: ['jquery.flot']
    },
    'jquery.flot.errorbars': {
      deps: ['jquery.flot']
    },
    'jquery.flot.axislabels': {
      deps: ['jquery.flot']
    },
    'jquery.flot.legendoncanvas': {
      deps: ['jquery.flot']
    },
  },
  config: {
      moment: {
          noGlobal: true
      }
  }
});

require(['routes/router'], function(App) {
  'use strict';
  new App();
});