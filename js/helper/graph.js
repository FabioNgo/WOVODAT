/** Use for overview and time serie graph only **/
define(function(require) {
  var MathHelper = require('helper/math');
  return {
    generateTick: function(min,max){
      var ticks = [];
      /** compute exponential Degree **/
      var expDeg = undefined
      if(MathHelper.exponentialDegree(min) < MathHelper.exponentialDegree(max)){
        expDeg = MathHelper.exponentialDegree(max);
      }else{
        expDeg = MathHelper.exponentialDegree(min)
      }
      var step = MathHelper.makeNumber((max-min)/8,expDeg); // step of ticks
      if(step == 0){
        step = 1;
      }
      /**** compute ticks ****/
      var startTick = MathHelper.makeNumber(min -step,expDeg); // start tick
      var endTick = MathHelper.makeNumber(max+step,expDeg); // end tick
      var curTick = startTick;
      if(curTick == endTick){
        ticks.push(curTick);
      }else{
        for(var i=0; curTick<endTick;i++){
          curTick = MathHelper.makeNumber(startTick + i *step,expDeg);
          ticks.push(curTick);
          
        }  
      }
      
      
      return ticks;
    },
    // setup effect for the graph
    formatGraphAppearance: function(data,timeSerieName, filterName, errorbars){
      
      return {
        data: data, //data is 3D array (y-error value is included in the data passed in)
        label: filterName + ":"+timeSerieName,
        // color: 0,
        lines: { 
          show: false
        },
        shadowSize: 3,
        points: {
          show: true,
          radius: 5,
          lineWidth: 2, // in pixels
          fill: true,
          fillColor: null,
          symbol: "circle",
          errorbars: errorbars, // parameter to enable error-bar presentation.
          yerr: {
            show: true,
            color: "red",
            upperCap: "-",
            lowerCap: "-",
          }
        },
      };
    },
  };
});