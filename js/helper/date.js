define(['moment'], function(moment) {
  return {
    formatDate: function(timeStamp) {
      return moment(timeStamp / 1000, 'X').utc().format('YYYY-MM-DD');
    }
  };
});