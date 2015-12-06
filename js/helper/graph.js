/** Use for overview and time serie graph only **/
define(function(require) {
  var MathHelper = require('helper/math');
  return {
    generateTick: function(min,max){
      var ticks = [];
      var numStep = 7;
      /** compute exponential Degree **/
      var expDeg = undefined
      if(MathHelper.exponentialDegree(min) < MathHelper.exponentialDegree(max)){
        expDeg = MathHelper.exponentialDegree(max);
      }else{
        expDeg = MathHelper.exponentialDegree(min)
      }
      var step = MathHelper.roundNumber((max-min)/numStep,expDeg); // step of ticks
      //if step is 0.xxx in computing exponential Degree, decrement expDeg
      while(step == 0){
        expDeg--;
        step = MathHelper.roundNumber((max-min)/numStep,expDeg);
      }
      min = MathHelper.roundNumber(min,expDeg);
      max = MathHelper.roundNumber(max,expDeg);

      /**** compute ticks ****/
      var startTick = MathHelper.roundNumber(min -step,expDeg); // start tick
      var endTick = MathHelper.roundNumber(max+step,expDeg); // end tick
      var curTick = startTick;
      if(curTick == endTick){
        ticks.push(curTick);
      }else{
        for(var i=0; curTick<endTick;i++){
          curTick = MathHelper.roundNumber(startTick + i *step,expDeg);
          ticks.push(curTick);
          
        }  
      }
      
      
      return ticks;
    },
    // setup effect for the graph
    formatGraphAppearance: function(data,timeSerieName, filterName,style, errorbars){
      
      var dataParam = {
        data: data, //data is 3D array (y-error value is included in the data passed in)
        label: filterName + ":"+timeSerieName,
        // color: 0,
        lines: { 
          show: false
        },
        shadowSize: 3,
        points: {
          show: false,
          radius: 3,
          lineWidth: 2, // in pixels
          fill: false,
          fillColor: null,
          symbol: "circle",
          
        },
        bars: {
          show: false,
          lineWidth: 2,
          barWidth: 1,
          fill: false,
          fillColor: 0,
          align: "left", // "left", "right", or "center"
          horizontal: false,
          zero: true
        }
      };
      if(errorbars){
        dataParam.points.errorbars = "y";
        dataParam.points.yerr = {
            show: true,
            color: "red",
            upperCap: "-",
            lowerCap: "-",
        }
      };
      if(style == 'bar'){
        dataParam.bars = {show: true};
      }
      else if(style == 'dot'){
        dataParam.points = {show: true, fill: true, fillColor: "#000000"};
        console.log(dataParam);
      }
      else if(style == 'circle'){
        dataParam.points = {show: true, fill: false};
        console.log(dataParam);
      }
      else if(style == 'horizontalbar'){
        dataParam.bars = {show: true};
        // Have not accounted for the case horizontal bar with no start time and end time
        console.log(dataParam);
      }
           // parameter to enable error-bar presentation.
      return dataParam;
    },
  };
});