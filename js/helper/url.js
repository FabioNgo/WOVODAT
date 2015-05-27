define(function() {
  return {
    getParam: function(param) {
      var i,
          query_string = {},
          query = window.location.search.substring(1),
          vars = query.split('&'),
          pair,
          arr;

      for (i = 0; i < vars.length; i++) {
        pair = vars[i].split('=');
        if (typeof query_string[pair[0]] === 'undefined') {
          query_string[pair[0]] = pair[1];
        } else if (typeof query_string[pair[0]] === 'string') {
          arr = [query_string[pair[0]], pair[1]];
          query_string[pair[0]] = arr;
        } else {
          query_string[pair[0]].push(pair[1]);
        }
      } 
      return query_string[param];
    }
  }
});