<?php
/**
 *	This class supports query the data from data table dd_edm 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_edmManager extends DeformationTablesManager {  
	
	protected function setColumnsName(){
		$result = array("dd_edm_line");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_edm";
	}
	protected function setDataType(){
		return "EDM";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id1","ds_id2");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code","sta_code1");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "dd_edm";
		$errorbar = true;
		$style = "dot";
		// var_dump($component);
		if($component == 'EDM Line Length'){    
			$unit = "m";
			$attribute = "dd_edm_line";
			$query = "select a.dd_edm_cerr as err ,a.dd_edm_time as time, a.$attribute as value  from $table as a where a.ds_id1=%s and a.ds_id2=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 