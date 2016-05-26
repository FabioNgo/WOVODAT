<?php
/**
 *	This class supports query the data from data table fd_gra 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class fd_graManager extends FieldsTablesManager {
	
	protected function setColumnsName(){
		$result = array("fd_gra_fstr");
		return $result;
	}
	protected function setTableName(){
		return "es_fd_gra";
	}
	
	protected function setDataType(){
		return "Gravity Fields";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("fs_id","fs_id_ref");
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
		$table = "fd_gra";
		$errorbar = true;
		$style = "dot";
		if($component == 'Gravity'){
			$unit = "Gal";
			$attribute = "fd_gra_fstr";
			$query = "select a.fd_gra_ferr as err ,a.fd_gra_time as time, a.$attribute as value $cc from $table as a where a.fs_id=%s and a.fs_id_ref=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 