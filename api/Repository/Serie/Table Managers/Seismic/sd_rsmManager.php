<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_rsmManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("sd_rsm_count");
		return $result;
	}
	protected function setTableName(){
		return "es_sd_rsm";
	}
	protected function setMonitoryType(){
		return "Seismic";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "SSAM";
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
		global $db;
		$unit="";
		$attribute = "";
		$query = "";
		$table = "sd_rsm";
		$errorbar = false;
		$style = "bar";
		$query1 = "select a.sd_sam_id from sd_rsm as a";
		$db->query($query1);
		$rsm_ids = $db->getList();
		foreach ($rsm_ids as $rsm_id) {
			$id = $rsm_id["sd_sam_id"];
		}

		if($component == 'RSAM Count'){
			$unit = "counts";
			$attribute = "sd_rsm_count";
			$query = "select a.sd_rsm_stime as stime, a.$attribute as value from $table  as a where a.sd_sam_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 