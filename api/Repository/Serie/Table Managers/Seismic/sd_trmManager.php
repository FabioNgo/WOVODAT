<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_trmManager extends SeismicTablesManager {
	
	protected function setColumnsName(){
		$result = array("sd_trm_domfreq1","sd_trm_domfreq2","sd_trm_maxamp","sd_trm_reddis");
		return $result;
	}
	protected function setTableName(){
		return "es_sd_trm";
	}
	
	protected function setDataType(){
		return "SeismicTremor";
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
		$table = "sd_trm";
		$errorbar = false;
		$style = "bar";
		if($component == 'Tremor Dominant Frequency-1'){
			$unit = "Hz";
			$attribute = "sd_trm_domfreq1";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Dominant Frequency-2'){
			$unit = "Hz";
			$attribute = "sd_trm_domfreq2";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Max-Amplitude'){
			$unit = "cm";
			$attribute = "sd_trm_maxamp";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Reduced Displacement'){
			$unit = "cm2";
			$attribute = "sd_trm_reddis";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 