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
    formatData: function(graph,filters,allowErrorbar){
     var minX = undefined,
         maxX = undefined,
         minY = undefined,
         maxY = undefined,
         data = [],
         errorbars = undefined;
      for(var i=0;i<filters.length;i++){
        var filter = filters[i];
        for(var j=0;j<filter.name.length;j++){
          filterName = filter.name[j];
          var list = [];
          var filterData = filter.timeSerie.getDataFromFilter(filterName)
          var style = filter.timeSerie.get('data').style; // plot style [bar,circle,dot,horizontalbar]
          var errorbar;
          if(!allowErrorbar){
            errorbar = false;
          }else{
            errorbar = filter.timeSerie.get('data').errorbar; // has error bar or not [true,false]
          }
          

          filterData.forEach(function(d) {
            var maxTime;
            var minTime;
            var upperBound = undefined;
            var lowerBound = undefined;
            var error;
            if(errorbar){
              error = d.error;
            }else{
              error = 0;
            };
            var value = d.value
            if(style == 'bar' || style == 'horizontalbar'){
              maxTime = d.etime
              minTime = d.stime;
            }
            else if(style == 'dot' || style == 'circle'){
              maxTime = minTime = d.time;
            }
            if (minX === undefined || minTime < minX){
              minX = minTime;
            }
            if (maxX === undefined || maxTime > maxX){
              maxX = maxTime;
            }
            if (minY === undefined || value-error < minY){
              minY = value-error;
            }
            if (maxY === undefined || value+error > maxY){
              maxY = value+error;
            }
            
            var tempData =  [];
            // parameters for bar data: left, right,bottom, top,error
            if(style == 'bar'){
              tempData.push(d.stime,d.etime,0,d.value);
            }
            else if(style == 'horizontalbar'){
              
              tempData.push(d.stime,d.etime,d.value,d.value);
              
            }
            else if(style == 'dot' || style == 'circle'){
              tempData.push(d['time'],d['value']);
            };

            if(errorbar){
              tempData.push(error);
            }
            list.push(tempData);
          });
          // console.log(list);
          var styleParams = {
            style: style,
            errorbar: errorbar,
          }
          data.push(this.formatGraphAppearance(list,filter.timeSerie.getName(),filterName,styleParams));
          
          
        }

          
      }
      
      graph.minX = minX-86400000;
      graph.maxX = maxX+86400000;
      
      
      /** setup y-axis tick **/
      if(maxY != undefined && minY != undefined){
        maxY = maxY*1.1;//1.1
        minY = minY*0.9;
        if(minY == maxY){
          minY = minY*0.5;
          maxY = maxY*1.5; 
        }
        graph.ticks = this.generateTick(minY,maxY);
        graph.minY = graph.ticks[0];
        graph.maxY = graph.ticks[graph.ticks.length-1]
        graph.ticks.push();
      }
      graph.timeRange.set({
        'startTime': graph.minX,
        'endTime': graph.maxX,
      });
      // graph.timeRange.trigger('change');
      graph.data = data;
    },
    /** setup effect for the graph
    *   data : data for floting
    *   filterName: filter name
    *   styleParams: params for styling graph {barwith,errorbar....}
    **/
    formatGraphAppearance: function(data,timeSerieName, filterName,styleParams){
      
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
          // wovodat: true;
          show: false,
          fullparams: true,
          lineWidth: 2,
          barWidth:0,
          fill: false,
          fillColor: 0,
          align: "left", // "left", "right", or "center"
          horizontal: false,
          zero: true
        }
      };
      if(styleParams.errorbar){
        dataParam.points.errorbars = "y";
        dataParam.points.yerr = {
            show: true,
            color: "red",
            upperCap: "-",
            lowerCap: "-",
        }
      };
      if(styleParams.style == 'bar'){
        dataParam.bars = {show: true};
      }
      else if(styleParams.style == 'dot'){
        dataParam.points = {show: true, fill: true, fillColor: "#000000"};
        // console.log(dataParam);
      }
      else if(styleParams.style == 'circle'){
        dataParam.points = {show: true, fill: false};
        // console.log(dataParam);
      }
      else if(styleParams.style == 'horizontalbar'){
        dataParam.bars.show = true;
        dataParam.bars.horizontal = true;
        // Have not accounted for the case horizontal bar with no start time and end time
        // console.log(dataParam);
      }
           // parameter to enable error-bar presentation.
      return dataParam;
    },
  };
});