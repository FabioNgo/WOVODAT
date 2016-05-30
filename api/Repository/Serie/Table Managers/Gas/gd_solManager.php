<?php
/**
 *	This class supports query the data from data table gd_sol 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class gd_solManager extends GasTablesManager {
	
	protected function setColumnsName(){
		$result = array("gd_sol_tflux","gd_sol_high","gd_sol_htemp");
		return $result;
	}
	protected function setTableName(){  
		return "es_gd_sol";
	}
	protected function setMonitoryType(){
		return "Gas";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "Soil Efflux";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("gs_id","gs_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("gs_code","gs_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "gd_sol";
		$errorbar = false;
		$style = "dot";
		if($component == 'Total Gas Flux'){      

			$attribute = "gd_sol_tflux";
			$errorbar = true;
			$query = "select a.gd_sol_units as unit, a.gd_sol_species as filter, a.gd_sol_time as time,  a.gd_sol_tflux_err as err, a.$attribute as value from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
	
		}else if($component == 'Highest Gas Flux'){
			$unit ="g/m2/d";
			$attribute = "gd_sol_high";
			$query = "select  a.gd_sol_species as filter, a.gd_sol_time as time, a.$attribute as value from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Highest Temperature'){
			$unit ="oC";
			$attribute = "gd_sol_htemp";
			$query = "select a.gd_sol_time as time, a.gd_sol_species as filter, a.$attribute as value from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 