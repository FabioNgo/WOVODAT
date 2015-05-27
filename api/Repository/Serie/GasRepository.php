<?php

class GasRepository { 
	public static $infor;

	/**
	*	@param volcanoId
	* @return List of Gas Time Serie 
	*/
	public static function getTimeSeriesList( $vd_id ) {
		$result = array();

		global $db;

		$query = "(select  c.gs_code FROM cn a, gs c where a.vd_id = %d and a.cn_id = c.cn_id and a.cn_pubdate <=now() and c.gs_pubdate <= now()) UNION (select c.gs_code FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = %d and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 and c.gs_pubdate <= now() ORDER BY c.gs_code)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();

		

		foreach (self::$infor as $key => $value) {
			$temp = call_user_func_array("self::getTimeSeriesList_".$key, array($vd_id, $stations));
			$result = array_merge($result, $temp );
		}
		return $result;
	}

	/**
	*	@param volcanoId
	* @return List of time serie from Gd_sol table
	*/
	private static function getTimeSeriesList_gd_sol( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["gs_code"]; 
			foreach (self::$infor["gd_sol"]["params"] as $type) {
				
				$cols = $type["cols"];
				$query = "select gd_sol_id from gs , gd_sol where gs.gs_code = %s and gs.gs_id = gd_sol.gs_id and gs.gs_pubdate <= now() and gd_sol.gd_sol_pubdate <= now() and gd_sol.$cols is not null limit 0 , 1";
				$db->query( $query, $code );

				if ( !$db->noRow() ) {
					$x = array('category' => "Gas",
										 'data_type' => self::$infor["gd_sol"]["data_type"],
										 'station_code' => $code,
										 'component' => $type["name"]	);
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
					array_push($result,  $x );
				} 
			}
		}
		
		return $result;
	}

	/**
	*	@param volcanoId
	* @return List of time serie from Gd_plu table
	*/
	private static function getTimeSeriesList_gd_plu( $vd_id , $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["gs_code"]; 
			foreach (self::$infor["gd_plu"]["params"] as $type) {
				
				$cols = $type["cols"];
				$query = "select gd_plu_id from gs , gd_plu where gs.gs_code = %s and gs.gs_id = gd_plu.gs_id and gs.gs_pubdate <= now() and gd_plu.gd_plu_pubdate <= now() and gd_plu.$cols is not null  limit 0 , 1";
				$db->query( $query, $code );

				if ( !$db->noRow() ) {
					$x = array('category' => "Gas",
										 'data_type' => self::$infor["gd_plu"]["data_type"],
										 'station_code' => $code,
										 'component' => $type["name"]	);
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
					array_push($result,  $x );
				} 
			}
		}

		$query = "select distinct cs.cs_code from cs, gd_plu where cs.cs_id = gd_plu.cs_id and gd_plu.vd_id = %d and cs.cs_pubdate <= now() and gd_plu.gd_plu_pubdate <= now()";
		$db->query( $query, $vd_id );
		$networks = $db->getList(); 

		foreach ($networks as $network) {
			$code = $network["cs_code"];
			foreach (self::$infor["gd_plu"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "select gd_plu_id from cs, gd_plu where cs.cs_code = %s and cs.cs_id = gd_plu.cs_id and gd_plu.vd_id = %d and cs.cs_pubdate <= now() and gd_plu.gd_plu_pubdate <= now() and gd_plu.$cols is not null limit 0 , 1";
				$db->query( $query, $code, $vd_id );
				if ( !$db->noRow() ) {
					$x = array('category' => "Gas",
										 'data_type' => self::$infor["gd_plu"]["data_type"],
										 'station_code' => $code,
										 'component' => $type["name"]	);
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
					array_push($result,  $x );
				}
			}
		}

		return $result;
	}

	/**
	*	@param volcanoId
	* 	@return List of time serie from Gd table
	*/
	private static function getTimeSeriesList_gd( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["gs_code"]; 
			foreach (self::$infor["gd"]["params"] as $type) {
				
				$cols = $type["cols"];
				$query = "select gd_id from gs , gd where gs.gs_code = %s and gs.gs_id = gd.gs_id and gs.gs_pubdate <= now() and gd.gd_pubdate <= now() and gd.$cols is not null limit 0 , 1";
				$db->query( $query, $code );

				if ( !$db->noRow() ) {
					$x = array('category' => "Gas",
										 'data_type' => self::$infor["gd"]["data_type"],
										 'station_code' => $code,
										 'component' => $type["name"]	);
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
					array_push($result,  $x );
				} 
			}
		}
		
		return $result;
	}

	/**
	*	Load data for specific datatype and station
	*	@param 
	*		$table : data type
	*		$code : station code
	*		$component : column name
	*	@return array of data
	*/
	public static function getStationData( $table, $code, $component ) {
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $code, $component) );
	}

	public static function getStationData_gd( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["gd"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "select b.gd_time, b.$attribute $filterQuery $cc from gs a, gd b where a.gs_code = %s and b.gs_id = a.gs_id and b.$attribute is not null and a.gs_pubdate <= now() and b.gd_pubdate <= now() order by b.gd_time desc";
			
			$db->query($query, $code);
			$res = $db->getList();
		}

		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["gd_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_gd_plu( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";

		foreach (self::$infor["gd_plu"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "select b.gd_time, b.$attribute $filterQuery $cc from gs a, gd b where a.gs_code = %s and b.gs_id = a.gs_id and b.$attribute is not null and a.gs_pubdate <= now() and b.gd_pubdate <= now() order by b.gd_time desc";

			$query =  "select distinct b.gd_plu_time, b.$attribute $filterQuery $cc  from gs a, gd_plu b, cs c where (a.gs_code = %s and b.gs_id = a.gs_id and a.gs_pubdate <= now()) or (b.cs_id = c.cs_id and c.cs_code = %s ) and b.gd_plu_pubdate <= now() and b.$attribute is not null order by b.gd_plu_time desc";
			
			$db->query($query, $code, $code);
			$res = $db->getList();
		}

		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["gd_plu_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

	public static function getStationData_gd_sol( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["gd_sol"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}
			$query = "select b.gd_sol_time, b.$attribute $filterQuery $cc from gs a, gd_sol b where a.gs_code = %s and b.gs_id = a.gs_id and b.$attribute is not null and a.gs_pubdate <= now() and b.gd_sol_pubdate <= now() order by b.gd_sol_time desc";
			
			$db->query($query, $code);
			$res = $db->getList();
		}
	
		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["gd_sol_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

}

GasRepository::$infor = json_decode( file_get_contents("Gas.json", true) , true);
