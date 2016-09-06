<?php
/**
 *	This class supports query the data from data table sd_evn
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class sd_evn_loc_eq_countsManager extends sd_evnManager {
	
	protected function setColumnsName(){
		$result = array("loc_eq_counts");
		return $result;
	}
	protected function setTableName(){
		return "";
	}
	
	protected function setDataType(){
		return "SeismicEventFromNetwork";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("cc_id","cc_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("cc_code","cc_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function getTimeSeriesListQuery($vd_id){
		$query = "select \"Located Earthquake Counts\" as loc_eq_counts, b.vd_inf_slat as vd_lat, b.vd_inf_slon as vd_long, a.cc_id as sta_id1,  a.cc_id as sta_id2 from sd_evn as a, vd_inf as b where ABS( b.vd_inf_slat - sd_evn_elat) < 1 AND ABS( b.vd_inf_slon - sd_evn_elon) < 6 AND (6371*2*ATAN2(SQRT(SIN((RADIANS(sd_evn_elat)-RADIANS( b.vd_inf_slat))/2)*SIN((RADIANS(sd_evn_elat)-RADIANS( b.vd_inf_slat))/2)+SIN((RADIANS(sd_evn_elon)-RADIANS( b.vd_inf_slon))/2)*SIN((RADIANS(sd_evn_elon)-RADIANS( b.vd_inf_slon))/2)*COS(RADIANS( b.vd_inf_slat))*COS(RADIANS(sd_evn_elat))),SQRT(1-(SIN((RADIANS(sd_evn_elat)-RADIANS( b.vd_inf_slat))/2)*SIN((RADIANS(sd_evn_elat)-RADIANS( b.vd_inf_slat))/2)+SIN((RADIANS(sd_evn_elon)-RADIANS( b.vd_inf_slon))/2)*SIN((RADIANS(sd_evn_elon)-RADIANS( b.vd_inf_slon))/2)*COS(RADIANS( b.vd_inf_slat))*COS(RADIANS(sd_evn_elat)))))) < 100 and b.vd_id = $vd_id group by b.vd_id, sta_id1, sta_id2 order by b.vd_id";
		return $query;
	}
	protected function setStationDataParams($component){
		$unit="counts";
		$attribute = "";
		$query = "";
		$table = "sd_evn";
		$errorbar = false;
		$style = "bar";
		$vd_long = $this->vd_long;
		$vd_lat = $this->vd_lat;
		// var_dump($this);
		if($component == 'Located Earthquake Counts'){
			$query = 'select count(sd_evn_edep) as value,  concat(DATE(sd_evn_time)," 00:00:00") as stime, concat(DATE(sd_evn_time), " 23:59:59") as etime, sd_evn_eqtype, cc_id, sd_evn_derr FROM sd_evn WHERE cc_id =%s AND sd_evn_pmag IS NOT NULL AND sd_evn_pubdate <= now() and sd_evn_edep BETWEEN -10 AND 40 GROUP BY DATE(sd_evn_time) order by sd_evn_time desc';
		}

		// echo $query;
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
    protected function setShortDataType()
    {
        return "";
    }
} 