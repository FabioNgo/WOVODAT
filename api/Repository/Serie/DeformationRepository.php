<?php 
class DeformationRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "(SELECT  c.ds_code,c.ds_nlat,c.ds_nlon FROM cn a, ds c  WHERE a.vd_id = %d AND a.cn_id = c.cn_id  ORDER BY c.ds_code) UNION (SELECT c.ds_code,c.ds_nlat,c.ds_nlon FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = %d AND a.vd_id=d.vd_id AND a.jj_net_flag = 'C' AND a.jj_net_id = c.cn_id AND (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20 ORDER BY c.ds_code)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();
		//var_dump($stations);
		foreach (self::$infor as $key => $value) 	
			if ( method_exists( "DeformationRepository", "getTimeSeriesList_".$key) ){
				$temp = call_user_func_array("self::getTimeSeriesList_".$key, array($vd_id, $stations));
				$result = array_merge($result, $temp );
			}
		return $result;
	}

	private static function getTimeSeriesList_dd_tlt( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_tlt"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_tlt b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_tlt"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_edm( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_edm"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.dd_edm_id from ds a, dd_edm b where a.ds_code = %s and (a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_edm"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_tlv( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_tlv"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_tlv b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_tlv"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_str( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_str"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_str b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_str"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_ang( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_ang"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_ang b where a.ds_code = %s and (a.ds_id = b.ds_id or a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_ang"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_gps( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_gps"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_gps b where a.ds_code = %s and (a.ds_id = b.ds_id or a.ds_id = b.ds_id_ref1 or a.ds_id = b.ds_id_ref2) and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_gps"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_gpv( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_gpv"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.ds_id from ds a, dd_gpv b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_gpv"]["data_type"],
							   'station_code' => $code,
							   'component' => $type["name"] );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
			}
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_lev( $vd_id, $stations ) {
		$result = array();
		global $db;
		foreach ($stations as $station) {
			$code = $station["ds_code"];
			foreach (self::$infor["dd_lev"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "SELECT b.dd_lev_time from ds a, dd_lev b where a.ds_code = %s and (a.ds_id = b.ds_id_ref or a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and b.$cols is not null limit 0 , 1";
				$db->query( $query, $code );
				if ( !$db->noRow() ) {
					$x = array('category' => "Deformation" ,
							   'data_type' => self::$infor["dd_lev"]["data_type"],
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

	public static function getStationData_dd_tlt( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_tlt"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_tlt_time, b.dd_tlt_timecsec, b.$attribute $filterQuery $cc from ds a, dd_tlt b,(select UNIX_TIMESTAMP(b.dd_tlt_time) as max from ds a , dd_tlt b where a.ds_code = %s and a.ds_id = b.ds_id limit 1) as c where a.ds_code = %s and a.ds_id = b.ds_id and (c.max - UNIX_TIMESTAMP(b.dd_tlt_time)) mod 43200 < 600  and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now() and b.$attribute is not null order by b.dd_tlt_time desc";
			$db->query($query, $code, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["dd_tlt_time"]);
			if ( !is_null( $row["dd_tlt_timecsec"] ) ) $time += floatval( $row["dd_tlt_timecsec"] );
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_edm( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_edm"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_edm_time, b.$attribute $filterQuery $cc from ds a, dd_edm b where a.ds_code = %s and (a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_edm_pubdate <= now() and b.$attribute is not null order by b.dd_edm_time desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["dd_edm_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_tlv( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_tlv"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_tlv_stime, b.dd_tlv_etime, b.$attribute $filterQuery $cc from ds a, dd_tlv b where a.ds_code = %s and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlv_pubdate <= now() and b.$attribute is not null order by $ desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$temp = array(  "stime" => 1000*strtotime($row["dd_tlv_stime"]) ,
							"etime" => 1000*strtotime($row["dd_tlv_etime"]) ,
							"value" => floatval($row[$attribute]) );
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = "";
			}
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_str( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_str"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_str_time, b.$attribute $filterQuery $cc FROM ds a, dd_str b WHERE a.ds_code = %s AND a.ds_id = b.ds_id AND a.ds_pubdate <= now() AND b.dd_str_pubdate <= now() AND b.$attribute IS NOT NULL ORDER BY b.dd_str_time DESC";
			$db->query($query, $code, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["dd_str_time"]);
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_ang( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_ang"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_ang_time, b.$attribute $filterQuery $cc from ds a, dd_ang b where a.ds_code = %s and (a.ds_id = b.ds_id or a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() and b.$attribute is not null order by b.dd_ang_time desc";
			$db->query($query, $code, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["dd_ang_time"]);
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_gps( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_gps"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_gps_time, b.$attribute $filterQuery $cc from ds a, dd_gps b where a.ds_code = %s and (a.ds_id = b.ds_id or a.ds_id = b.ds_id_ref1 or a.ds_id = b.ds_id_ref2) and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() and b.$attribute is not null order by b.dd_gps_time desc";
			$db->query($query, $code, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["dd_gps_time"]);
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_gpv( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_gpv"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_gpv_stime, b.dd_gpv_etime, b.$attribute $filterQuery $cc from ds a, dd_gpv b where a.ds_code = %s and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() and b.$attribute is not null order by b.dd_gpv_stime desc";
			$db->query($query, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$temp = array(  "stime" => 1000*strtotime($row["dd_gpv_stime"]) ,
							"etime" => 1000*strtotime($row["dd_gpv_etime"]) ,
							"value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_dd_lev( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["dd_lev"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "SELECT b.dd_lev_time, b.$attribute $filterQuery $cc from ds a, dd_lev b where a.ds_code = %s and (a.ds_id = b.ds_id_ref or a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_lev_pubdate <= now() and b.$attribute is not null order by b.dd_lev_time desc";
			$db->query($query, $code, $code);
			$res = $db->getList();
		}
		foreach ($res as $row) {
			$time = strtotime($row["dd_lev_time"]);
			$temp = array( "time" => intval(1000 * $time) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}
}

DeformationRepository::$infor = json_decode( file_get_contents("Deformation.json", true) , true);

