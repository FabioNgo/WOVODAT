<?php 
class HydrologyRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();

		global $db;
		$query = "(select  c.hs_code,c.hs_lat,c.hs_lon FROM cn a, hs c where a.vd_id = %d and a.cn_id = c.cn_id) UNION (select c.hs_code,c.hs_lat,c.hs_lon FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = %d and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 ORDER BY c.hs_code)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();

		//var_dump($stations);

		foreach (self::$infor as $key => $value) 	
			if ( method_exists( "HydrologyRepository", "getTimeSeriesList_".$key) ){
				$temp = call_user_func_array("self::getTimeSeriesList_".$key, array($vd_id, $stations));
				$result = array_merge($result, $temp );
			}
		return $result;
	}

	private static function getTimeSeriesList_hd( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["hs_code"];
			foreach (self::$infor["hd"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "select hd_id from hs, hd where hs_code = %s and hs.hs_id = hd.hs_id and hd.$cols is not null limit 0 , 1";
				$db->query( $query, $code );

				if ( !$db->noRow() ) {
					$x = array('category' => "Hydrology" ,
							   'data_type' => self::$infor["hd"]["data_type"],
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

	public static function getStationData_hd( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["hd"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}

			$query = "select b.hd_time, b.$attribute $filterQuery $cc from hs a, hd b where a.hs_code = %s and a.hs_id = b.hs_id and b.$attribute is not null and a.hs_pubdate <= now() and b.hd_pubdate <= now() order by b.hd_time desc";
			
			$db->query($query, $code);
			$res = $db->getList();
		}

		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["hd_time"]) , 
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
}

HydrologyRepository::$infor = json_decode( file_get_contents("Hydrology.json", true) , true);