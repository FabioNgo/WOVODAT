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
    formatGraphAppearance: function(data,timeSerieName, filterName){
      
      return {
        data: data,
        label: filterName + ":"+timeSerieName,
        // color: 0,
        lines: { 
          show: false
        },
        shadowSize: 3,
        points: {
          show: true,
          radius: 2,
          lineWidth: 2, // in pixels
          fill: true,
          fillColor: "#000000",
          symbol: "circle" 
        },
      }
    },
  };
});