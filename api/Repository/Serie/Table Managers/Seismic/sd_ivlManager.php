<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_ivlManager extends SeismicTablesManager {

	protected function setColumnsName(){
		$result = array(	"sd_ivl_hdist","sd_ivl_avgdepth","sd_ivl_vdispers",
							"sd_ivl_hmigr_hyp","sd_ivl_vmigr_hyp","sd_ivl_nrec",
							"sd_ivl_nfelt","sd_ivl_etot","sd_ivl_fmin","sd_ivl_fmax","sd_ivl_amin","sd_ivl_amax"
							);
		return $result;
	}
	protected function setIdsRelationship(){
        return "or";
    }

    protected function setTableName(){
		return "es_sd_ivl";
	}

	protected function setDataType(){
		return "SeismicInterval";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ss_id","sn_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code","sta_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.

    protected function getStationCodeQuery($sta_id){
        $sta_id_code_query ="";
        if($sta_id == "sn_id"){
            $sta_id_code_query = "SELECT c.sn_id as sta_id, c.sn_code as sta_code FROM sn as c where c.sn_id IN (SELECT distinct sn_id from sd_ivl)";
        }
        if($sta_id == "ss_id"){
            $sta_id_code_query = "SELECT c.ss_id as sta_id, c.ss_code as sta_code FROM ss as c where c.ss_id IN (SELECT distinct ss_id from sd_ivl)";
        }
        // echo($sta_id_code_query."\n");
        return $sta_id_code_query;

    }
	protected function setStationDataParams($component){
		$unit="";
		$query = "";
		$table = "sd_ivl";
		$errorbar = false;
		$style = "bar";
		if($component == 'Swarm Distance'){
			$unit = "km";
			$attribute = "sd_ivl_hdist";
			$query = "select a.sd_ivl_eqtype  as filter ,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Mean Depth'){
			$unit = "km";
			$attribute = "sd_ivl_avgdepth";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value   from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Vertical Dispersion'){
			$unit = "km";
			$attribute = "sd_ivl_vdispers";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Horizontal Migration'){
			$unit = "km";
			$attribute = "sd_ivl_hmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Vertical Migration'){
			$unit = "km";
			$attribute = "sd_ivl_vmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Counts'){
			$unit = "counts";
			$attribute = "sd_ivl_nrec";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Felt Earthquake Counts'){
			$unit = "counts";
			$attribute = "sd_ivl_nfelt";
			$query = "select a.sd_ivl_eqtype as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}
		else if($component == 'Total Seismic Energy'){
			$unit = "Erg";
			$attribute = "sd_ivl_etot";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Minimum Frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Maximum Frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Minimum Amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Maximum Amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  from $table  as a where (a.ss_id=%s or a.sn_id=%s) and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]

    protected function setShortDataType()
    {
        // TODO: Implement setShortDataType() method.
        return "Interval";
    }
}