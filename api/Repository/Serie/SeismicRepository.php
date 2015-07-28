<?php 

class SeismicRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "(select c.ss_code,c.ss_lat,c.ss_lon FROM sn a, ss c  where a.vd_id = %d  and a.sn_id = c.sn_id) UNION (select c.ss_code,c.ss_lat,c.ss_lon FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = %d and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();
		foreach (self::$infor as $key => $value) 	
			if ( method_exists( "SeismicRepository", "getTimeSeriesList_".$key) ) {
				$temp = call_user_func_array("self::getTimeSeriesList_".$key, array($vd_id, $stations));
				$result = array_merge($result, $temp );
			}
		return $result;
	}

	private static function getTimeSeriesList_sd_evs( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ss_code"];
			foreach (self::$infor["sd_evs"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.sd_evs_id FROM ss a, sd_evs b where a.ss_code = %s and a.ss_id = b.ss_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_evs"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_sd_int( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach (self::$infor["sd_int"]["params"] as $type) {
			$cols = $type["cols"];
			$query = "SELECT a.sd_int_id FROM sd_int a where a.vd_id = %d and a.$cols is not null limit 0 , 1";
			$db->query( $query, $vd_id );
			if ( !$db->noRow() ) {
				$x = array('category' => "Seismic" ,
						   'data_type' => self::$infor["sd_int"]["data_type"],
						   'volcanoID' => $vd_id,
						   'component' => $type["name"] );
				$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["volcanoID"].$x["component"] );
	 			array_push($result,  $x );
			}
		}
		//var_dump($vd_id);
		return $result;
	}

	private static function getTimeSeriesList_sd_trm( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["ss_code"];
			foreach (self::$infor["sd_trm"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.sd_trm_id FROM ss a, sd_trm b where a.ss_code = %s and (a.ss_id = b.ss_id || ( b.ss_id is null and b.sn_id = a.sn_id ) ) and a.ss_pubdate <= now() and b.sd_trm_pubdate <= now() and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_trm"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		
		return $result;
	}

	private static function getTimeSeriesList_sd_ivl( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["ss_code"];
			foreach (self::$infor["sd_ivl"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.sd_ivl_id FROM ss a, sd_ivl b where a.ss_code = %s and (a.ss_id = b.ss_id || ( b.ss_id is null and b.sn_id = a.sn_id ) ) and a.ss_pubdate <= now() and b.sd_ivl_pubdate <= now() and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_ivl"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		
		return $result;
	}

	private static function getTimeSeriesList_sd_rsm( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ss_code"];
			foreach (self::$infor["sd_rsm"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT c.sd_rsm_id FROM ss a, sd_sam b, sd_rsm c where a.ss_code = %s and a.ss_id = b.ss_id and b.sd_sam_id = c.sd_sam_id and c.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_rsm"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_sd_ssm( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ss_code"];
			foreach (self::$infor["sd_ssm"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT c.sd_ssm_id FROM ss a, sd_sam b, sd_ssm c where a.ss_code = %s and a.ss_id = b.ss_id and b.sd_sam_id = c.sd_sam_id and c.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_ssm"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_sd_evn( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query = "SELECT a.sn_code FROM sn a WHERE a.vd_id = %d";
		$db->query( $query , $vd_id );
		$networks = $db->getList();

		//var_dump($networks);

		foreach ($networks as $network) {
			$code = $network["sn_code"];

			foreach (self::$infor["sd_evn"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.sd_evn_id FROM sn a, sd_evn b where a.sn_code = %s and a.sn_id = b.sn_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Seismic" ,
							   'data_type' => self::$infor["sd_evn"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
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

		foreach (self::$infor["sd_ivl"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			// var_dump($attribute);
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.sd_ivl_stime, b.sd_ivl_etime, b.$attribute $filterQuery $cc from ss a, sd_ivl b where a.ss_code = %s and (a.ss_id = b.ss_id || ( b.ss_id is null and b.sn_id = a.sn_id ) ) and a.ss_pubdate <= now() and b.sd_ivl_pubdate <= now() and b.$attribute is not null order by b.sd_ivl_stime desc";	
			$db->query($query, $code);
			$res = $db->getList();
			//var_dump($res);
		}
		foreach ($res as $row) {
			
			$stime = strtotime($row["sd_ivl_stime"]);
// var_dump(1000*$stime);
			$etime = strtotime($row["sd_ivl_etime"]);

			$temp = array( "stime" => floatval(1000 * $stime) ,
						   "etime" => floatval(1000 * $etime) ,
						   "time" => floatval(1000*($stime+$etime)/2),
										 "value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
			// var_dump($temp);
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