<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_levManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("dd_lev_delev");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_lev";
	}
	protected function setMonitoryType(){
		return "Deformation";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "Leveling";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id1","ds_id2");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code1","sta_code2");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "dd_lev";
		$errorbar = true;
		$style = "horizontalbar";
		if($component == 'Elevation Change'){
			$unit = "mm";
			$attribute = "dd_lev_delev";
			$query = "select a.ds_id2 as filter, a.dd_lev_herr as err ,a.dd_lev_time as time, a.$attribute as value   from $table as a where a.ds_id1=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 