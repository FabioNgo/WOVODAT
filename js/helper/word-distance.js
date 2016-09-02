/** Use for overview and time serie graph only **/
define(function(require) {
  var _ = require("vendor/underscore/underscore-min");
  'use strict';
  // var this = require('helper/math');
  return {
    Levenshtein : function( str_m, str_n ) { 
      var previous, current, matrix;
    // Constructor
      matrix = this._matrix = [];

    // Sanity checks
      if ( str_m == str_n )
        return this.distance = 0;
      else if ( str_m == '' )
        return this.distance = str_n.length;
      else if ( str_n == '' )
        return this.distance = str_m.length;
      else {
        // Danger Will Robinson
        previous = [ 0 ];
        _.forEach( str_m, function( v, i ) { i++; previous[ i ] = i } );

        matrix[0] = previous;
        _.forEach( str_n, function( n_val, n_idx ) {
          current = [ ++n_idx ];
          _.forEach( str_m, function( m_val, m_idx ) {
            m_idx++;
            if ( str_m.charAt( m_idx - 1 ) == str_n.charAt( n_idx - 1 ) )
              current[ m_idx ] = previous[ m_idx - 1 ];
            else
              current[ m_idx ] = Math.min
                ( previous[ m_idx ]     + 1   // Deletion
                , current[  m_idx - 1 ] + 1   // Insertion
                , previous[ m_idx - 1 ] + 1   // Subtraction
                )
          });
          previous = current;
          matrix[ matrix.length ] = previous
        });

        return this.distance = current[ current.length - 1 ]
      }
    },
    hammingDistance: function(str1,str2) {
      var dist = 0;
      // console.log(str1);
      str1 = str1.toLowerCase();
      str2 = str2.toLowerCase();

       for(var i = 0; i < str1.length; i++) {

          if(str2[i] && str2[i] !== str1[i]) {
              dist += Math.abs(str1.charCodeAt(i) - str2.charCodeAt(i)) + Math.abs(str2.indexOf( str1[i] )) * 2;
          } 
          else if(!str2[i]) {
              //  If there's no letter in the comparing string
              dist += dist;
          }
      }
      return dist;
    }
  };
});