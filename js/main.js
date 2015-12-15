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
    'hammer':'vendor/materialize/hammerjs',
    'velocity':'//cdnjs.cloudflare.com/ajax/libs/velocity/1.2.3/velocity.min',
    'jquery.flot': 'vendor/jquery-flot/src/jquery.flot',
    'jquery.colorhelpers' : 'vendor/jquery-flot/lib/jquery.colorhelpers',
    'jquery.drag' : 'vendor/jquery-flot/lib/jquery.drag',
    'jquery.mousewheel' : 'vendor/jquery-flot/lib/jquery.mousewheel',
    'jquery.resize' : 'vendor/jquery-flot/lib/jquery.resize',
    'jquery.flot.navigate': 'vendor/jquery-flot/src/plugins/jquery.flot.navigate',
    'jquery.flot.selection': 'vendor/jquery-flot/src/plugins/jquery.flot.selection',
    'jquery.flot.time': 'vendor/jquery-flot/src/plugins/jquery.flot.time',
    'excanvas' : 'vendor/jquery-flot/lib/excanvas',
    'jquery.flot.tickrotor': 'vendor/jquery-flot/src/plugins/jquery.flot.tickrotor',
    'jquery.flot.errorbars': 'vendor/jquery-flot/src/plugins/jquery.flot.errorbars',
    'jquery.flot.axislabels': 'vendor/jquery-flot/src/plugins/jquery.flot.axislabels',
    'jquery.flot.legendoncanvas' :'vendor/jquery-flot/src/plugins/jquery.flot.legendoncanvas',    
  },
  shim: {
    'jquery' : {
      exports: '$'
    },
    'backbone': {
      deps: ['underscore', 'jquery'],
      exports: 'Backbone'
    },
    'material': {
      deps: ['jquery', 'hammer', 'velocity']
    },
    'jquery.flot': {
      deps: ['jquery','excanvas','jquery.colorhelpers','jquery.mousewheel','jquery.resize'],
      exports: '$.flot'
    },
    'jquery.flot.selection': {
      deps: ['jquery.flot']
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
define(function(require) {
  require(['routes/router'], function(App) {
    'use strict';
    new App();
  });
})