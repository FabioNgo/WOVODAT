<?php 
class MeteoRepository {
	public static $infor;

	public static function getTimeSeriesList( $vd_id ) {
		$result = array();

		global $db;

		//$query = "(select  c.ms_code FROM cn a, ms c where a.vd_id = %d and a.cn_id = c.cn_id)";

		$query = "(select c.ms_code FROM cn a, ms c where a.vd_id = %d and a.cn_id = c.cn_id) UNION (select c.ms_code FROM jj_volnet a, ms c,vd_inf d WHERE a.vd_id = %d and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ms_lat, 2) + power(d.vd_inf_slon - c.ms_lon, 2))*100)<30 ORDER BY c.ms_code)";
		$db->query( $query, $vd_id, $vd_id );
		$stations = $db->getList();

		foreach (self::$infor as $key => $value) {
			$temp = call_user_func_array("self::getTimeSeriesList_".$key, array($vd_id, $stations));
			$result = array_merge($result, $temp );
		}
		return $result;
	}

	public static function getTimeSeriesList_med( $vd_id, $stations ) {
		$result = array();
		global $db;

		foreach ($stations as $station) {
			$code = $station["ms_code"];
			foreach (self::$infor["med"]["params"] as $type) {
				$cols = $type["cols"];
				$query = "select med_id from ms, med where ms_code = %s and ms.ms_id = med.ms_id and med.$cols is not null  limit 0 , 1";
				$db->query( $query, $code );

				if ( !$db->noRow() ) {
					$x = array('category' => "Meteo" ,
							   'data_type' => self::$infor["med"]["data_type"],
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

	public static function getStationData_med( $code, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		foreach (self::$infor["med"]["params"] as $type) if ( $type["name"] == $component ) {
			$attribute = $type["cols"];
			if ( array_key_exists("filter", $type) ) {
				$filter = $type["filter"];
				$filterQuery = ", b.".$filter;
			}

			$query = "select b.med_time, b.$attribute $filterQuery $cc from ms a, med b where a.ms_code = %s and a.ms_id = b.ms_id and b.$attribute is not null and a.ms_pubdate <= now() and b.med_pubdate <= now() order by b.med_time desc";
			
			$db->query($query, $code);
			$res = $db->getList();
		}

		foreach ($res as $row) {
			$temp = array( "time" => 1000*strtotime($row["med_time"]) , 
										 "value" => floatval($row[$attribute]) );
			if ($filter != "") 
				$temp["filter"] = $row[$filter];
			array_push($result, $temp );			
		}
		return $result;
	}

}

MeteoRepository::$infor = json_decode( file_get_contents("Meteo.json", true) , true);

