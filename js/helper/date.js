define(['moment'], function(moment) {
  return {
    formatDate: function(timeStamp) {
      //console.log(timeStamp);
      return moment(timeStamp/1000, 'X').utc().format('YYYY-MM-DD');
    },
    formatSerieTime : function(timeStamp) {
      //console.log(timeStamp);
      var m =  moment(timeStamp/1000, 'X').utc()
      var result = m.format('YYYY-MM-DD HH:mm:ss');
      if ( m.millisecond() != 0 ) result += "." +  ( Math.floor(m.millisecond() / 10) ).toString();
      return result;  
    },
    dateToMillisecond : function(date) {
      return moment.utc(date, "YYYY-MM-DD").valueOf();
    }
  };
});