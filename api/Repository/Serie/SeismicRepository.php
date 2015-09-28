<?php 

class SeismicRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "(select c.ss_code,c.ss_lat,c.ss_lon FROM sn a, ss c  where a.vd_id = %d  and a.sn_id = c.sn_id) UNION (select c.ss_code,c.ss_lat,c.ss_lon FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = %d and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();
		$result = self::getTimeSeriesList_sd_evs($vd_id,$stations);

		$result = array_merge($result,self::getTimeSeriesList_sd_evn($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_ivl($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_int($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_rsm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_ssm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_trm($vd_id,$stations));

		return $result;
	}

	private static function getTimeSeriesList_sd_evs( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('S-P Arrival Time') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_spint IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Epicenter From Event') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_dist_actven IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Max-amplitude') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_maxamptrac IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Dominant Frequency') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_domFre IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Magnitude') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_mag IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Energy') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_evs_energy IS NOT NULL 


			
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicEventFromNetwork",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_int( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Distance Felt') as type from jjcn_sta as a, sd_int as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_int_maxdist IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Intensity') as type from jjcn_sta as a, sd_int as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_int_maxrint IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Distance at Max-intensity') as type from jjcn_sta as a, sd_int as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_int_maxrint_dist IS NOT NULL 
			


			
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicIntensity",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_trm( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Tremor Dominant Frequency-1') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_trm_domfreq1 IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Tremor Dominant Frequency-2') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_trm_domfreq2 IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Tremor Max-Amplitude') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_trm_maxamp IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Reduced Displacement') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_trm_reddis IS NOT NULL	
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicTremor",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_ivl( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Counts') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_nrec IS NOT NULL 
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Swarm Distance') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_hdist IS NOT NULL 
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Swarm Mean Depth') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_avgdepth IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Swarm Vertical Dispersion') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_vdispers IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Hypocenter Horiz-Migration') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_hmigr_hyp IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Hypocenter Vert-Migration') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_vmigr_hyp IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Total Seismic Energy') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_nfelt IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Felt Earthquake Counts') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_etot IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Min Frequency') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_fmin IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Max Frequency') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_fmax IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Min Amplitude') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_amin IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Max Amplitude') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ivl_amax IS NOT NULL

			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicInterval",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_sd_rsm( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('RSAM Counts') as type from jjcn_sta as a, sd_rsm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_rsm_count IS NOT NULL 
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "RSAM",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_ssm( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('SSAM Low-freq Limit') as type from jjcn_sta as a, sd_ssm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ssm_lowf IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('SSAM Hight-freq Limit') as type from jjcn_sta as a, sd_ssm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ssm_hightf IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('SSAM Counts') as type from jjcn_sta as a, sd_ssm as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.ss_id and b.sd_ssm_count IS NOT NULL 
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id );
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SSAM",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_evn( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Depth') as type from jjcn_sta as a, sd_evn as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.cc_id and b.sd_evn_edep IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Earthquake Magnitude') as type from jjcn_sta as a, sd_evn as b where a.type='Seismic' and a.vd_id=%d and a.sta_id=b.cc_id and b.sd_evn_pmag IS NOT NULL
			
			";



		$db->query( $query, $vd_id,$vd_id);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicEventFromNetwork",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	public static function getStationData( $table, $code, $component ) {
		foreach (self::$infor as $key => $type) {
			if ( $type["data_type"] == $table ) {
				// var_dump(self::$infor);
				return call_user_func_array("self::getStationData_".$key, array( $code, $component) );
				
			}
		}
	} 

	public static function getStationData_sd_evs( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["sd_evs"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.sd_evs_time, b.sd_evs_time_ms, b.$attribute $filterQuery $cc from ss a, sd_evs b where a.ss_code = %s and b.ss_id = a.ss_id and a.ss_pubdate <= now() and b.sd_evs_pubdate <= now() and b.$attribute is not null order by b.sd_evs_time desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["sd_evs_time"]);
			if ( !is_null( $row["sd_evs_time_ms"] ) ) $time += floatval( $row["sd_evs_time_ms"] );
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_sd_int( $vd_id, $component ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["sd_int"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT a.sd_int_time, a.$attribute $filterQuery $cc from sd_int a where a.vd_id = %d and a.sd_int_pubdate <= now() and a.$attribute is not null order by a.sd_int_time desc";
			$db->query($query, $vd_id);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["sd_int_time"]);
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_sd_rsm( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["sd_rsm"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			// echo($attribute);
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT c.sd_rsm_stime, c.$attribute $filterQuery $cc from ss a,sd_sam b,sd_rsm c where a.ss_code = %s and b.ss_id = a.ss_id and b.sd_sam_id = c.sd_sam_id and a.ss_pubdate <= now() and b.sd_sam_pubdate <= now() and c.$attribute is not null order by c.sd_rsm_stime desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["sd_rsm_stime"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_sd_ssm( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["sd_ssm"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT c.sd_ssm_stime, c.$attribute $filterQuery $cc from ss a,sd_sam b,sd_ssm c where a.ss_code = %s and b.ss_id = a.ss_id and b.sd_sam_id = c.sd_sam_id and a.ss_pubdate <= now() and b.sd_sam_pubdate <= now() and c.$attribute is not null order by c.sd_ssm_stime desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["sd_ssm_stime"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_sd_evn( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["sd_evn"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.sd_evn_time, b.$attribute $filterQuery $cc from sn a,sd_evn b where a.sn_code = %s and b.sn_id = a.sn_id and a.sn_pubdate <= now() and b.sd_evn_pubdate <= now() and b.$attribute is not null order by b.sd_evn_time desc";
			$db->query($query, $code);
			$res = $db->getList();

		}
		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["sd_evn_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_sd_ivl( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		if($component == 'Swarm Distance'){
				$attribute = "sd_ivl_hdist";
				$query = "select sd_ivl_eqtype  as filter ,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_hdist IS NOT NULL";
	
		}else if($component == 'Swarm Mean Depth'){
			$attribute = "sd_ivl_avgdepth";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_avgdepth IS NOT NULL";

		}else if($component == 'Swarm Vertical Dispersion'){
			$attribute = "sd_ivl_vdispers";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_vdispers IS NOT NULL";
		}else if($component == 'Hypocenter Horiz-Migration'){
			$attribute = "sd_ivl_hmigr_hyp";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_hmigr_hyp IS NOT NULL";
		}else if($component == 'Hypocenter Vert-Migration'){
			$attribute = "sd_ivl_vmigr_hyp";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_vmigr_hyp IS NOT NULL";
		}else if($component == 'Earthquake Counts'){
			$attribute = "sd_ivl_nrec";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_nrec IS NOT NULL";
		}else if($component == 'Total Seismic Energy'){
			$attribute = "sd_ivl_nfelt";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_nfelt IS NOT NULL";
		}else if($component == 'Felt Earthquake Counts'){
			$attribute = "sd_ivl_etot";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_etot IS NOT NULL";
		}else if($component == 'Earthquake Min Frequency'){
			$attribute = "sd_ivl_fmin";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_fmin IS NOT NULL";
		}else if($component == 'Earthquake Max Frequency'){
			$attribute = "sd_ivl_fmax";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_fmax IS NOT NULL";
		}else if($component == 'Earthquake Min Amplitude'){
			$attribute = "sd_ivl_amin";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_amin IS NOT NULL";
		}else if($component == 'Earthquake Max Amplitude'){
			$attribute = "sd_ivl_amax";
			$query = "select sd_ivl_eqtype  as filter,a.dd_tlt_stime as stime,a.dd_tlt_etime as etime, a.$attribute as value from sd_ivl  as a where a.ss_id=%s and a.sd_ivl_amax IS NOT NULL";
		}

		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( 	"stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"filter" => $row["filter"]
						);
			
			
			array_push($result, $temp );
		}
		return $result;
	}

	public static function getStationData_sd_trm( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		$filterQuery1 = "";
		$filter1 = "";
		foreach (self::$infor["sd_trm"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			if ( array_key_exists("filter1", $type) ) {
				$filter1 = $type["filter1"];
				$filterQuery1 = ", b.".$filter1;
			}
			$query = "SELECT b.sd_trm_stime, b.sd_trm_etime, b.$attribute $filterQuery $filterQuery1 $cc from ss a, sd_trm b where a.ss_code = %s and (a.ss_id = b.ss_id || ( b.ss_id is null and b.sn_id = a.sn_id ) ) and a.ss_pubdate <= now() and b.sd_trm_pubdate <= now() and b.$attribute is not null order by b.sd_trm_stime desc";	
			$db->query($query, $code);
			$res = $db->getList();
			//var_dump($res);
		}
		foreach ($res as $row) {
			$stime = strtotime($row["sd_trm_stime"]);
			$etime = strtotime($row["sd_trm_etime"]);
			$temp = array( "stime" => intval(1000 * $stime) ,
						   "etime" => intval(1000 * $etime) ,
						   "time" => floatval(1000*($stime+$etime)/2),
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") {
				$temp["filter"] = $row[$filter];
				if ( is_null($temp["filter"]) ) 
					$temp["filter"] = "Not defined";
			}
			if ($filter1 != "") {
				$temp["filter1"] = $row[$filter1];
				if ( is_null($temp["filter1"]) ) 
					$temp["filter1"] = "Not defined";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

}

SeismicRepository::$infor = json_decode( file_get_contents("Seismic.json", true) , true);