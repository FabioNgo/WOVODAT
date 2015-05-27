<?php 

class SeismicRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "(select  c.ss_code,c.ss_lat,c.ss_lon FROM sn a, ss c  where a.vd_id = %d  and a.sn_id = c.sn_id) UNION (select c.ss_code,c.ss_lat,c.ss_lon FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = %d and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();
		//var_dump($stations);
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

	public static function getStationData( $table, $code, $component ) {
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $code, $component) );
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
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
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
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
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
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
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
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}
}

SeismicRepository::$infor = json_decode( file_get_contents("Seismic.json", true) , true);