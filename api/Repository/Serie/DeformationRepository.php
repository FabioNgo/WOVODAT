<?php 
class DeformationRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		// echo ("asdas");
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = %d AND a.type='Deformation' ";
		// var_dump($query);
		$db->query( $query, $vd_id);
		$stations = $db->getList();
		// var_dump($stations);
		$result = self::getTimeSeriesList_dd_tlt($vd_id,$stations);
		$result = array_merge($result,self::getTimeSeriesList_dd_tlv($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_dd_str($vd_id,$stations));
		// array_push(array, var)
		// var_dump($result);
		return $result;
	}

	private static function getTimeSeriesList_dd_tlt( $vd_id, $stations ) {
		$result = array();
		global $db;
				

			$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt1') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_tlt1 IS NOT NULL 	
				union 
				select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt2') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_tlt2 IS NOT NULL 
				union
				select distinct a.sta_id,a.sta_code as ds_code,concat('Temp') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_tlt_temp IS NOT NULL ";



				$db->query( $query, $vd_id,$vd_id,$vd_id );
				
				$serie_list = $db->getList();

				for ($i=0; $i<sizeof($serie_list) - 1 ; $i++) { 
					$serie = $serie_list[$i];
						$x = array('category' => "Deformation" ,
							   'data_type' => "ElectronicTilt",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie["type"],
							   'id' => $serie["sta_id"] 
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}	
				
			// }
		// }
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

		// foreach ($stations as $station) {
		// 	$code = $station["ds_code"];
		// 	foreach (self::$infor["dd_tlv"]["params"] as $type) {
		// 		$cols = $type["cols"];
		// 		$query = "SELECT b.ds_id from ds a, dd_tlv b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
		// 		$db->query( $query, $code );
		// 		if ( !$db->noRow() ) {
		// 			$x = array('category' => "Deformation" ,
		// 					   'data_type' => self::$infor["dd_tlv"]["data_type"],
		// 					   'station_code' => $code,
		// 					   'component' => $type["name"] );
		// 			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		//  			array_push($result,  $x );
		// 		}
		// 	}
		// }
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt Mag') as type from jjcn_sta as a, dd_tlv as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_tvl_mag IS NOT NULL 	
				union 
				select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt Azimuth') as type from jjcn_sta as a, dd_tlv as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_tlv_azi IS NOT NULL ";



				$db->query( $query, $vd_id,$vd_id,$vd_id );
				
				$serie_list = $db->getList();
				// var_dump($serie_list);
				for ($i=0; $i<sizeof($serie_list) - 1 ; $i++) { 
					$serie = $serie_list[$i];
						$x = array('category' => "Deformation" ,
							   'data_type' => "TitltVector",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie["type"],
							   'id' => $serie["sta_id"] 
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
		return $result;
	}

	private static function getTimeSeriesList_dd_str( $vd_id, $stations ) {

		$result = array();
		global $db;
		// foreach ($stations as $station) {
		// 	$code = $station["ds_code"];
		// 	foreach (self::$infor["dd_str"]["params"] as $type) {
		// 		$cols = $type["cols"];
		// 		$query = "SELECT b.ds_id from ds a, dd_str b where a.ds_code = %s and a.ds_id = b.ds_id and b.$cols is not null limit 0 , 1";
		// 		$db->query( $query, $code );
		// 		if ( !$db->noRow() ) {
		// 			$x = array('category' => "Deformation" ,
		// 					   'data_type' => self::$infor["dd_str"]["data_type"],
		// 					   'station_code' => $code,
		// 					   'component' => $type["name"] );
		// 			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		//  			array_push($result,  $x );
		// 		}
		// 	}
		// }
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_comp1 IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_comp2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_comp3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-4') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_comp4 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Volumetric Strain change') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_vdstr IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_sstr_ax1 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_sstr_ax2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_sstr_ax3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear azimuth axis-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_azi_ax1 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear azimuth axis-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_azi_ax2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear azimuth axis-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_azi_ax3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Strain') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_pmax IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Min Strain') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_pmin IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Strain Direction') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_pmax_dir IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Min Strain Direction') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_pmin_dir IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Barometric Pressure') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=%d and a.sta_id=b.ds_id and b.dd_str_bpres IS NOT NULL

			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id, $vd_id,$vd_id,$vd_id,$vd_id,$vd_id,
			$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id,$vd_id);
			
		$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list) - 1 ; $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "Strain",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
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

	public static function getStationData( $table, $code, $component, $id ) {
		// echo("sdasfd");
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $code, $component,$id) );
	} 

	public static function getStationData_dd_tlt( $code, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$filterQuery = "";
		$filter = "";
		$query = "";
				if($component == 'Titlt1'){
						$attribute = "dd_tlt1";
						$query = "select a.dd_tlt_time,a.dd_tlt_timecsec, a.$attribute $cc from dd_tlt as a where a.ds_id=%s and a.dd_tlt1 IS NOT NULL";
			
				}
				else if($component == 'Titlt2'){
					$attribute = "dd_tlt2";
					$query = "select a.dd_tlt_time,a.dd_tlt_timecsec , a.$attribute $cc from dd_tlt as a where a.ds_id=%s and a.dd_tlt2 IS NOT NULL ";

				}else if($component == 'Temp'){
					$attribute = "dd_tlt_temp";
					$query = "select a.dd_tlt_time,a.dd_tlt_timecsec , a.$attribute $cc from dd_tlt as a where a.ds_id=%s and a.dd_tlt_temp IS NOT NULL";
				}
				$db->query($query, $id);

				$res = $db->getList();
			// }
		// }
		// var_dump($res);
		foreach ($res as $row) {
			// var_dump($row);
			$time = strtotime($row["dd_tlt_time"]);

			if ( !is_null( $row["dd_tlt_timecsec"] ) ){ 
				$time += floatval( $row["dd_tlt_timecsec"] );
			}

			$temp = array( "time" => intval(1000 * $time) ,
							"value" => floatval($row[$attribute]) 
						);
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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

DeformationRepository::$infor = json_decode( file_get_contents("Deformation.json", true) , true);

