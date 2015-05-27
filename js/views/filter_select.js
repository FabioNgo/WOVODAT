define(function(require) {
  'use strict';
  var $ = require('jquery'),
      Backbone = require('backbone'),
      _ = require('underscore'),
      template = require('text!templates/filter.html');
      
  return Backbone.View.extend({
    el: '',

    className : "mgt15",

    template: _.template(template),

    events: {
      'change input': 'onChange'
    },
    
    initialize: function(options) {
      _(this).bindAll('render', 'OptionForFilter','prepareDataAndRender', 'onChange' );
      this.filter = options.selectingFilter;
      this.filterObserver = options.filterObserver;

      this.listenTo(this.model, 'sync', this.prepareDataAndRender); 
         
    },

    render: function(options) {
      var displayName = this.model.getDisplayName();
      this.$el.html(this.template({
        options: options,
        name : displayName
      }));
    },
    
    prepareDataAndRender: function() {
      var options = {}, data = this.model.get('data');
      options = this.OptionForFilter();
      this.render(options);
      if ( options.length == 0 ) {
        this.filter.add({filter : undefined});
      }

    },
    
    OptionForFilter: function() {
      var data = this.model.get('data'),      
        list=[],
        a = [];
      data.forEach( function(ds) {
        if ( ds.filter && _.indexOf( list, ds.filter) == -1 ) 
          list.push(ds.filter); 
      });

      return list;
    },
    
    onChange: function(event) {
      var input = event.target,
          value = $(input).val();
      if ($(input).is(':checked')) {
        this.filter.add( { filter : value } );
      }
      else {
        this.filter.remove(this.filter.get(value));
      }
    },
    
    destroy: function() {
      // From StackOverflow with love.
      this.undelegateEvents();
      this.$el.removeData().unbind(); 
      this.remove();  
      Backbone.View.prototype.remove.call(this);
    }
  });
});