<?php
/**
 *	This class supports query the data from data table fd_mgv
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class fd_mgvManager extends FieldsTablesManager {
	
	protected function setColumnsName(){
		$result = array("fd_mgv_dec","fd_mgv_incl");
		return $result;
	}
	protected function setTableName(){
		return "es_fd_mgv";
	}
	
	protected function setDataType(){
		return "Magnetic Vector";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("fs_id","fs_id");
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
		$table = "fd_mgv";
		$errorbar = true;
		$style = "dot";
		if($component == 'Magnetic Declination'){
			$unit = "o";
			$attribute = "fd_mgv_dec";
			$query = "select a.fd_mgv_time as time, a.$attribute as value  from $table as a where a.fs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Inclination'){
			$unit = "o";
			$attribute = "fd_mgv_incl";
			$query = "select a.fd_mgv_time as time, a.$attribute as value from $table as a where a.fs_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 