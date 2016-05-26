<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_intManager extends SeismicTablesManager {
	
	protected function setColumnsName(){
		$result = array("sd_int_maxdist","sd_int_maxrint","sd_int_maxrint_dist");
		return $result;
	}
	protected function setTableName(){
		return "es_sd_int";
	}
	
	protected function setDataType(){
		return "SeismicIntensity";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ss_id","ss_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code","sta_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "sd_int";
		$errorbar = false;
		$style = "dot";
		if($component == 'Max Distance Felt'){
			$unit = "km";
			$attribute = "sd_int_maxdist";
			$query = "select a.sd_int_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Max-intensity'){
			$unit = "";
			$attribute = "sd_int_maxrint";
			$query = "select a.sd_int_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Distance at Max-intensity'){
			$unit = "km";
			$attribute = "sd_int_maxrint";
			$query = "select a.sd_int_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 