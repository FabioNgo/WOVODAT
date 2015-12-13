<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_ivlManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array(	"sd_ivl_hdist","sd_ivl_avgdepth","sd_ivl_vdispers",
							"sd_ivl_hmigr_hyp","sd_ivl_vmigr_hyp","sd_ivl_nrec",
							"sd_ivl_nfelt","sd_ivl_etot","sd_ivl_fmin","sd_ivl_fmax","sd_ivl_amin","sd_ivl_amax"
							);
		return $result;
	}
	protected function setTableName(){
		return "es_sd_ivl";
	}
	protected function setMonitoryType(){
		return "Seismic";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "SeismicInterval";
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
		$table = "sd_ivl";
		$errorbar = false;
		$style = "bar";
		if($component == 'Swarm Distance'){
			$unit = "km";
			$attribute = "sd_ivl_hdist";
			$query = "select a.sd_ivl_eqtype  as filter ,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Mean Depth'){
			$unit = "km";
			$attribute = "sd_ivl_avgdepth";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value   from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Vertical Dispersion'){
			$unit = "km";
			$attribute = "sd_ivl_vdispers";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Horiz-Migration'){
			$unit = "km";
			$attribute = "sd_ivl_hmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Vert-Migration'){
			$unit = "km";
			$attribute = "sd_ivl_vmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Counts'){
			$unit = "counts";
			$attribute = "sd_ivl_nrec";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Total Seismic Energy'){
			$unit = "counts";
			$attribute = "sd_ivl_nfelt";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Felt Earthquake Counts'){
			$unit = "Erg";
			$attribute = "sd_ivl_etot";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min-frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min-amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where a.ss_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 