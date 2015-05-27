require.config({
  paths: {
    // Vendors.
    'jquery': 'vendor/jquery/dist/jquery.min',
    'backbone': 'vendor/backbone-amd/backbone-min',
    'underscore': 'vendor/underscore-amd/underscore-min',
    'text': 'vendor/requirejs-text/text',
    'moment': 'vendor/moment/moment',
    'pace': 'vendor/pace/pace.min',

    'jquery.flot': 'vendor/jquery-flot/jquery.flot.wovodat',
    'jquery.flot.navigate': 'vendor/jquery-flot/jquery.flot.navigate',
    'jquery.flot.selection': 'vendor/jquery-flot/jquery.flot.selection',
    'jquery.flot.time': 'vendor/jquery-flot/jquery.flot.time',
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