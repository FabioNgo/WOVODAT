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
		$result = array_merge($result,self::getTimeSeriesList_dd_edm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_dd_ang($vd_id,$stations));
		// array_push(array, var)
		// var_dump($result);
		return $result;
	}

	private static function getTimeSeriesList_dd_tlt( $vd_id, $stations ) {
		$result = array();
		global $db;
				

			$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt1') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_tlt1 IS NOT NULL 	
				union 
				select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt2') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_tlt2 IS NOT NULL 
				union
				select distinct a.sta_id,a.sta_code as ds_code,concat('Temp') as type from jjcn_sta as a, dd_tlt as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_tlt_temp IS NOT NULL ";



			$db->query( $query);
			
			$serie_list = $db->getList();

			for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
				$serie = $serie_list[$i];
					$x = array('category' => "Deformation" ,
						   'data_type' => "ElectronicTilt",
						   'station_code' => $serie["ds_code"],
						   'component' => $serie["type"],
						   'sta_id' => $serie["sta_id"],
					
						   );
				$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
	 			array_push($result,  $x );
			}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_edm( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('EDM line length') as type from jjcn_sta as a, dd_edm as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_edm_line IS NOT NULL 	
			";
			$db->query( $query);
			$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "Strain",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
				
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_tlv( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt Mag') as type from jjcn_sta as a, dd_tlv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_tvl_mag IS NOT NULL 	
				union 
				select distinct a.sta_id,a.sta_code as ds_code,concat('Titlt Azimuth') as type from jjcn_sta as a, dd_tlv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_tlv_azi IS NOT NULL ";



				$db->query( $query);
				
				$serie_list = $db->getList();
				// var_dump($serie_list);
				for ($i=0; $i<sizeof($serie_list) ; $i++) { 
					$serie = $serie_list[$i];
						$x = array('category' => "Deformation" ,
							   'data_type' => "TitltVector",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie["type"],
							   'sta_id' => $serie["sta_id"],
						
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
		 			array_push($result,  $x );
				}
		return $result;
	}

	private static function getTimeSeriesList_dd_str( $vd_id, $stations ) {

		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_comp1 IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_comp2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_comp3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain Comp-4') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_comp4 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Volumetric Strain change') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_vdstr IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_sstr_ax1 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_sstr_ax2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Shear strain axis-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_sstr_ax3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain azimuth axis-1') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_azi_ax1 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain azimuth axis-2') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_azi_ax2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Strain azimuth axis-3') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_azi_ax3 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Strain') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_pmax IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Min Strain') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_pmin IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Max Strain Direction') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_pmax_dir IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Min Strain Direction') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_pmin_dir IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Barometric Pressure') as type from jjcn_sta as a, dd_str as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_str_bpres IS NOT NULL

			";



		$db->query( $query);
			
		$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "Strain",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
			
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_ang( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('Horizontal angle target-1') as type from jjcn_sta as a, dd_ang as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_arg_hort1 IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Horizontal angle target-2') as type from jjcn_sta as a, dd_ang as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_ang_hort2 IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Vertical angle target-1') as type from jjcn_sta as a, dd_ang as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_ang_vert1 IS NOT NULL
			";



		$db->query( $query);
			
		$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "Angle",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
				
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		
		return $result;
	}

	private static function getTimeSeriesList_dd_gps( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Latitude') as type from jjcn_sta as a, dd_gps as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gps_lat IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Longtitude') as type from jjcn_sta as a, dd_ang as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gps_lon IS NOT NULL
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Elevation') as type from jjcn_sta as a, dd_ang as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gps_elev IS NOT NULL

			";



		$db->query( $query);
			
		$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "GPSPosition&Slope",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_gpv( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Displacement') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_dmag IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Displ-azimuth') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_daz IS NOT NULL 	
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Displ-inclination') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_vincl IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS N-S Displ') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_N IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS E-W Displ') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_E IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Vertical Displ') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_vert IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS N-S Velocity') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_staVelNorth IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS E-W Velocity') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_staVelEast IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Vertical Velocity') as type from jjcn_sta as a, dd_gpv as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_gpv_staVelVert IS NOT NULL 	
			";



		$db->query( $query);
			
		$serie_list = $db->getList();
		// var_dump($serie_list);
		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
			$x = array('category' => "Deformation" ,
					   'data_type' => "GPSVector",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_dd_lev( $vd_id, $stations ) {
		// $result = array();
		// global $db;
		// $query="select distinct a.sta_id,a.sta_code as ds_code,concat('GPS Displacement') as type from jjcn_sta as a, dd_lev as b where a.type='Deformation' and a.vd_id=$vd_id and a.sta_id=b.ds_id and b.dd_lev_delev IS NOT NULL 	
		// 	";



		// $db->query( $query);
			
		// $serie_list = $db->getList();
		// // var_dump($serie_list);
		// for ($i=0; $i<sizeof($serie_list); $i++) { 
		// 	$serie = $serie_list[$i];
		// 	$x = array('category' => "Deformation" ,
		// 			   'data_type' => "GPSVector",
		// 			   'station_code' => $serie["ds_code"],
		// 			   'component' => $serie["type"],
		// 			   'sta_id' => $serie["sta_id"],
		// 			   );
		// 	$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 	// 		array_push($result,  $x );
		// }
		// return $result;
	}

	public static function getStationData( $table, $code, $component, $id ) {
		// echo("sdasfd");
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$id) );
	} 

	public static function getStationData_dd_tlt( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = true;
		$data = array();
		$filter = "";
		$query = "";
		if($component == 'Titlt1'){
			$attribute = "dd_tlt1";
			$query = "select a.dd_tlt_err1 as err ,a.dd_tlt_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}
		else if($component == 'Titlt2'){
			$attribute = "dd_tlt2";
			$query = "select a.dd_tlt_err2 as err ,a.dd_tlt_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Temp'){
			$attribute = "dd_tlt_temp";
			$errorbar = false;
			$query = "select a.dd_tlt_time as time, a.$attribute as value from $table as a where $cc a.ds_id=$id and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " "
						);
			
			if($errorbar){
				$temp["error"] = $row["err"];
			}
			array_push($data, $temp );			
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}

	public static function getStationData_dd_edm( $table, $component,$id) {
		// global $db;
		// $cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		// $result = array();
		// $res = array();
		// $attribute = "";
		// $filterQuery = "";
		// $filter = "";
		// if($component == 'Titlt1'){
		// 	$attribute = "dd_edm_line";
		// 	$query = "select a.dd_edm_cerr as err ,a.dd_tlt_time as time, a.$attribute as value from $table as a where a.ds_id=$id and a.dd_tlt1 IS NOT NULL";
		// }
		// else if($component == 'Titlt2'){
		// 	$attribute = "dd_tlt2";
		// 	$query = "select a.dd_tlt_err2 as err ,a.dd_tlt_time as time, a.$attribute as value from $table as a where a.ds_id=$id and a.dd_tlt2 IS NOT NULL";

		// }else if($component == 'Temp'){
		// 	$attribute = "dd_tlt_temp";
		// 	$query = "select a.dd_tlt_time as time, a.$attribute as value from dd_tlt as a where a.ds_id=$id and a.dd_tlt_temp IS NOT NULL";
		// }
		// $db->query($query, $id);

		// $res = $db->getList();
		// foreach ($res as $row) {
			
		// 	$time = strtotime($row["time"]);
		// 	$temp = array( "time" => floatval(1000 * $time) ,
		// 					"value" => floatval($row["value"]),
		// 					"error" => $row["err"],
		// 					"filter" => " "
		// 				);
			
			
		// 	array_push($result, $temp );
		// }
		// return $result;
	}

	public static function getStationData_dd_tlv( $table, $component,$id ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "horizontalbar";
		$errorbar = true;
		$data = array();
		$filter = "";
		if($component == 'Titlt Mag'){
			$attribute = "dd_tlv_mag";
			$query = "select a.dd_tlv_magerr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		} else if($component == 'Titlt Azimuth'){
			$attribute = "dd_tlv_azi";
			$query = "select a.dd_tlv_azierr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";

		}
		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( "stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"error" => $row["err"],
							"filter" => " "
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}

	public static function getStationData_dd_str( $table, $component,$id ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = true;
		$data = array();
		$filter = "";
		if($component == 'Strain Comp-1'){
				$attribute = "$table_comp1";
				$query = "select a.$table_err1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-2'){
				$attribute = "$table_comp2";
				$query = "select a.$table_err2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-3'){
				$attribute = "$table_comp3";
				$query = "select a.$table_err3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-4'){
				$attribute = "$table_comp4";
				$query = "select a.$table_err4 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Volumetric Strain change'){
				$attribute = "$table_vdstr";
				$query = "select a.$table_vdstr_err as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-1'){
				$attribute = "$table_sstr_ax1";
				$query = "select a.$table_stderr1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-2'){
				$attribute = "$table_sstr_ax2";
				$query = "select a.$table_stderr2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-3'){
				$attribute = "$table_sstr_ax3";
				$query = "select a.$table_stderr3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-1'){
				$attribute = "$table_azi_ax1";
				$errorbar = false;
				$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-2'){
				$attribute = "$table_azi_ax2";
				$errorbar = false;
				$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-3'){
				$attribute = "$table_azi_ax3";
				$errorbar = false;
				$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
				$attribute = "$table_pmax";
				$query = "select a.$table_pmaxerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
				$attribute = "$table_pmin";
				$query = "select a.$table_pminerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain Direction'){
				$attribute = "$table_pmax_dir";
				$query = "select a.$table_pmax_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Min Strain Direction'){
				$attribute = "$table_pmin_dir";
				$query = "select a.$table_pmin_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
				$attribute = "$table_bpres";
				$errorbar = false;
				$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}

		$db->query($query);

		$res = $db->getList();
		foreach ($res as $row) {
			if(!array_key_exists("err", $row)){
				$row["err"] = " ";
			}
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							
							"filter"=> " ",
						);
			if($errorbar){
				$temp["error"] = $row["err"];
			}
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}

	public static function getStationData_dd_ang( $table, $component ) {
		// global $db;
		// $cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		// $result = array();
		// $res = array();
		// $attribute = "";
		// $filterQuery = "";
		// $filter = "";
		// if($component == 'Horizontal Angle target-1'){
		// 	$attribute = "dd_tlv_mag";
		// 	$query = "select a.dd_tlv_magerr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		// } else if($component == 'Titlt Azimuth'){
		// 	$attribute = "dd_tlv_azi";
		// 	$query = "select a.dd_tlv_azierr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";

		// }
		// $db->query($query, $id);

		// $res = $db->getList();
		// foreach ($res as $row) {
			
		// 	$stime = strtotime($row["stime"]);
		// 	$etime = strtotime($row["etime"]);
		// 	$temp = array( "stime" => floatval(1000 * $stime) ,
		// 					"etime" => floatval(1000 * $etime) ,
		// 					"value" => floatval($row["value"]),
		// 					"error" => $row["err"],
		// 					"filter" => " "
		// 				);
			
		// 	array_push($result, $temp );
		// }
		// return $result;
	}

	public static function getStationData_dd_gps( $table, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = true;
		$data = array();
		$filter = "";
		if($component == 'GPS Latitude'){
			$attribute = "dd_gps_lat";
			$query = "select a.dd_gps_nserr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Longtitude'){
			$attribute = "dd_gps_lon";
			$query = "select a.dd_gps_ewerr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Elevation'){
			$attribute = "dd_gps_elev";
			$query = "select a.dd_gps_verr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"error" => $row["err"],
							"filter" => " "
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}

	public static function getStationData_dd_gpv( $table, $component ) {
		global $db;
		$cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "horizontalbar";
		$errorbar = true;
		$data = array();
		$filter = "";
		if($component == 'GPS Displacement'){
			$attribute = "dd_gpv_dmag";
			$query = "select a.dd_gpv_dherr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Displ'){
			$attribute = "dd_gpv_N";
			$query = "select a.dd_gpv_dnerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Displ'){
			$attribute = "dd_gpv_E";
			$query = "select a.dd_gpv_deerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Displ'){
			$attribute = "dd_gpv_vert";
			$query = "select a.dd_gpv_dverr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Velocity'){
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelNorthErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Velocity'){
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelEastErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Velocity'){
			$attribute = "dd_gpv_staVelVert";
			$query = "select a.dd_gpv_staVelVertErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}

		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( "stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"error" => $row["err"],
							"filter" => " "
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
	}

	public static function getStationData_dd_lev( $table, $component ) {
		// global $db;
		// $cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
		// $result = array();
		// $res = array();
		// $attribute = "";
		// $filterQuery = "";
		// $filter = "";
		// foreach (self::$infor["dd_lev"]["params"] as $type) if ( $type["name"] == $component ) {
		// 	$attribute = $type["cols"];
		// 	if ( array_key_exists("filter", $type) ) {
		// 		$filter = $type["filter"];
		// 		$filterQuery = ", b.".$filter;
		// 	}
		// 	$query = "SELECT b.dd_lev_time, b.$attribute $filterQuery $cc from ds a, dd_lev b where a.ds_code = $id and (a.ds_id = b.ds_id_ref or a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_lev_pubdate <= now() and b.$attribute is not null order by b.dd_lev_time desc";
		// 	$db->query($query, $table, $table);
		// 	$res = $db->getList();
		// }
		// foreach ($res as $row) {
		// 	$time = strtotime($row["dd_lev_time"]);
		// 	$temp = array( "time" => intval(1000 * $time) , 
		// 								 "value" => floatval($row[$attribute]) );
		// 	if ($filter != ""){
		// 		$temp["filter"] = $row[$filter];
		// 	}else{
		// 		$temp["filter"] = " ";
		// 	}
		// 	array_push($result, $temp );			
		// }
		// return $result;
	}
}

DeformationRepository::$infor = json_decode( file_get_contents("Deformation.json", true) , true);

