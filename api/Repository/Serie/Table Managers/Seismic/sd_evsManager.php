<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_evsManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("sd_evs_spint","sd_evs_dist_actven","sd_evs_maxamptrac","sd_evs_domFre","sd_evs_mag","sd_evs_energy");
		return $result;
	}
	protected function setTableName(){
		return "es_sd_evs";
	}
	protected function setMonitoryType(){
		return "Seismic";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "SeismicEventFromSingleStation";
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
		$table = "sd_evs";
		$errorbar = false;
		$style = "dot";
		if($component == 'S-P Arrival Time'){
			$unit = "s";
			$attribute = "sd_evs_spint";
			$query = "select a.sd_evs_eqtype  as filter,a.sd_evs_time as time, a.$attribute as value from $table  as a where a.sn_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Epicenter From Vent'){
			$unit = "km";
			$attribute = "sd_evs_dist_actven";
			$query = "select a.sd_evs_eqtype  as filter,a.sd_evs_time as time, a.$attribute as value from $table  as a where a.sn_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-amplitude '){
			$unit = "km";
			$attribute = "sd_evs_dist_actven";
			$query = "select a.sd_evs_eqtype  as filter,a.sd_evs_time as time, a.$attribute as value from $table  as a where a.sn_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 