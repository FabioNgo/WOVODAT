define(function(require) {
  "use strict";
  return {
    decimalPlaces: function(value) {
      if (Math.floor(value) === value ) return 0;
      return value.toString().split(".")[1].length || 0;
    },
    //expDegree always greater than expDegree of numberStr
    // Round the number to expDegree
    roundNumber: function(numberStr,desExpDegree){
      var desCoe; // destination Coefficient
    	var number = parseFloat(numberStr)
      number = number/ Math.pow(10,desExpDegree);
     //  	var sourceExpDegree = this.exponentialDegree(number); //expoential Degree of this number

     // 	var sourceCoe = this.coefficient(number);
     // 	if(sourceExpDegree >=desExpDegree){
     // 		var differExpDeg = sourceExpDegree-desExpDegree;
     //  		desCoe = sourceCoe * Math.pow(10,differExpDeg);
    	// }else{
     //    desCoe = 0;
     //  }
      	number = Math.round(number);
      	return number*Math.pow(10,desExpDegree);
    },
    exponentialDegree: function(value){
      	value = value.toExponential();
      	var a =  value.toString().split("e")[1];
      	var exp = parseInt(a);
      	return exp;
    },
    /** no decimal place **/
    coefficient: function(value){ 
      	value = value.toExponential();
      	var a =  value.toString().split("e")[0];
      	var coe = parseFloat(a);
      	coe = Math.round(coe);
      	return coe;
    },
    
  };
});