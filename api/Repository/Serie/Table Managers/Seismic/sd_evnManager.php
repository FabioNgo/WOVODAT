<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_evnManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("sd_evn_edep","sd_evn_pmag");
		return $result;
	}
	protected function setTableName(){
		return "es_sd_evn";
	}
	protected function setMonitoryType(){
		return "Seismic";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "SeismicEventFromNetwork";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("n_id","n_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("n_code","n_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "sd_evn";
		$errorbar = true;
		$style = "circle";
		if($component == 'S-P Arrival Time'){
			$unit = "s";
			$attribute = "sd_evs_spint";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Epicenter From Event'){
			$unit = "km";
			$attribute = "sd_evs_dist_actven";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-Amplitude'){
			$unit = "cm";
			$attribute = "sd_evs_maxamptrac";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Dominant Frequency'){
			$unit = "Hz";
			$attribute = "sd_evs_domFre";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Magnitude'){
			$unit = "Hz";
			$attribute = "sd_evs_mag";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Energy'){
			$unit = "Erg";
			$attribute = "sd_evs_energy";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 