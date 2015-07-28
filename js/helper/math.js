define(function() {
  return {
    decimalPlaces: function(value) {
      if (Math.floor(value) === value ) return 0;
      return value.toString().split(".")[1].length || 0;
    },
    //expDegree always greater than one of numberStr
    makeNumber: function(numberStr,expDegree){
    	var number = parseFloat(numberStr)
      	var exp = this.exponentialDegree(number);

     	var coe = this.coefficient(number);
     	if(exp < expDegree){
     		var differDeg = exp-expDegree;
      		coe = coe * Math.pow(10,differDeg);
      	}
      	coe = Math.round(coe);
      	return coe*Math.pow(10,expDegree);
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