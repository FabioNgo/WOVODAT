<?php 
class DeformationRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Deformation' ";
		$db->query( $query);
		$stations = $db->getList();
		$result = self::getTimeSeriesList_dd_tlt($vd_id,$stations);
		$result = array_merge($result,self::getTimeSeriesList_dd_tlv($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_dd_str($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_dd_edm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_dd_ang($vd_id,$stations));
		return $result;
	}

	private static function getTimeSeriesList_dd_tlt( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_tlt1","dd_tlt2","dd_tlt_temp");
		$table_name = "es_dd_tlt";
		$query = "select a.ds_id,a.ds_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "ElectronicTilt",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_edm( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_edm_lin");
		$table_name = "es_dd_edm";
		$query = "select a.ds_id1,a.ds_code1,a.ds_id2,a.ds_code2";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "ElectronicTilt",
							   'station_code1' => $serie["ds_code1"],
							   'station_code2' => $serie["ds_code2"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id1' => $serie["ds_id1"],
							   'ds_id2' => $serie["ds_id2"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
 			array_push($result,  $x );
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_tlv( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_tlv_mag","dd_tlv_azi");
		$table_name = "es_dd_tlv";
		$query = "select a.ds_id,a.ds_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "TitltVector",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_str( $vd_id, $stations ) {

		$result = array();
		global $db;
		$cols_name = array("dd_str_comp1","dd_str_comp2","dd_str_comp3","dd_str_comp4","dd_str_vdstr","dd_str_sstr_ax1","dd_str_sstr_ax2",
					"dd_str_sstr_ax3","dd_str_azi_ax1","dd_str_azi_ax2","dd_str_azi_ax3","dd_str_pmax","dd_str_pmin");
		$table_name = "es_dd_str";
		$query = "select a.ds_id,a.ds_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "Strain",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_ang( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_ang_hort1","dd_ang_hort2","dd_ang_vert1","dd_ang_vert2");
		$table_name = "es_dd_ang";
		$query = "select a.ds_id1,a.ds_code1,a.ds_id2,a.ds_code2";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "Angle",
							   'station_code1' => $serie["ds_code1"],
							   'station_code2' => $serie["ds_code2"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id1' => $serie["ds_id1"],
							   'ds_id2' => $serie["ds_id2"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
 			array_push($result,  $x );
		}	
			return $result;	
		
	}

	private static function getTimeSeriesList_dd_gps( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_gps_lat","dd_gps_lon","dd_gps_elev","dd_gps_slope");
		$table_name = "es_dd_gps";
		$query = "select a.ds_id,a.ds_code,a.ds_id_ref1,a.ds_code1,a.ds_id_ref2,a.ds_code2";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "GPSPosition&Slope",
							   'station_code' => $serie["ds_code"],
							   'station_code1' => $serie["ds_code1"],
							   'station_code2' => $serie["ds_code2"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id"],
							   'ds_id1' => $serie["ds_id_ref1"],
							   'ds_id2' => $serie["ds_id_ref2"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["station_code1"].$x["station_code2"].$x["component"] );
 			array_push($result,  $x );
		}	
		
	}

	private static function getTimeSeriesList_dd_gpv( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_gpv_dmag","dd_gpv_daz","dd_gpv_vincl","dd_gpv_N","dd_gpv_E","dd_gpv_vert","dd_gpv_staVelNorth","dd_gpv_staVelEast",
		"dd_gpv_staVelVert");
		$table_name = "es_dd_gpv";
		$query = "select a.ds_id,a.ds_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "GPSVector",
							   'station_code' => $serie["ds_code"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_dd_lev( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("dd_lev_delev");
		$table_name = "es_dd_lev";
		$query = "select a.ds_id_ref,a.ds_code,a.ds_id1,a.ds_code1,a.ds_id2,a.ds_code2";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Deformation" ,
							   'data_type' => "Leveling",
							   'station_code' => $serie["ds_code"],
							   'station_code1' => $serie["ds_code1"],
							   'station_code2' => $serie["ds_code2"],
							   'component' => $serie[$cols_name[$j]],
							   'ds_id' => $serie["ds_id_ref"],
							   'ds_id1' => $serie["ds_id1"],
							   'ds_id2' => $serie["ds_id2"],
							   );
				}
			}
				
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["station_code1"].$x["station_code2"].$x["component"] );
 			array_push($result,  $x );
		}	
	}

	public static function getStationData( $table, $code, $component, $ids ) {
		// echo("sdasfd");
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$ids) );
	} 

	public static function getStationData_dd_tlt( $table, $component,$ids ) {
		// $id = $ids["ds_id"];
		global $db;
		// echo($ids);
		$id = $ids["ds_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = true;
		$data = array();
		$filter = "";
		$query = "";
		$unit = "";
		if($component == 'Radial/X-axis Tilt'){
			$unit = "urad";
			$attribute = "dd_tlt1";
			$query = "select a.dd_tlt_err1 as err ,a.dd_tlt_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}
		else if($component == 'Tangential/Y-axis Tilt'){
			$unit = "urad";
			$attribute = "dd_tlt2";
			$query = "select a.dd_tlt_err2 as err ,a.dd_tlt_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Temp'){
			$unit = "oC";
			$attribute = "dd_tlt_temp";
			$errorbar = false;
			$query = "select a.dd_tlt_time as time, a.$attribute as value from $table as a where $cc a.ds_id=$id and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$db->query($query, $id);
		// echo($query);	
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
		$result["unit"] = $unit;
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
		$unit = "";
		if($component == 'Titlt Mag'){
			$unit = "urad";
			$attribute = "dd_tlv_mag";
			$query = "select a.dd_tlv_magerr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		} else if($component == 'Titlt Azimuth'){
			$unit = "o";
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
		$result["unit"] = $unit;
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
		$unit = "";
		if($component == 'Strain Comp-1'){
			$unit = "ustrain";
			$attribute = "$table_comp1";
			$query = "select a.$table_err1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-2'){
			$unit = "ustrain";
			$attribute = "$table_comp2";
			$query = "select a.$table_err2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-3'){
			$unit = "ustrain";
			$attribute = "$table_comp3";
			$query = "select a.$table_err3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-4'){
			$unit = "ustrain";
			$attribute = "$table_comp4";
			$query = "select a.$table_err4 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Volumetric Strain change'){
			$unit = "ustrain";
			$attribute = "$table_vdstr";
			$query = "select a.$table_vdstr_err as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-1'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax1";
			$query = "select a.$table_stderr1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-2'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax2";
			$query = "select a.$table_stderr2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-3'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax3";
			$query = "select a.$table_stderr3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-1'){
			$unit = "o";
			$attribute = "$table_azi_ax1";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-2'){
			$unit = "o";
			$attribute = "$table_azi_ax2";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-3'){
			$unit = "o";
			$attribute = "$table_azi_ax3";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
			$unit = "ustrain";
			$attribute = "$table_pmax";
			$query = "select a.$table_pmaxerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
			$unit = "ustrain";
			$attribute = "$table_pmin";
			$query = "select a.$table_pminerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain Direction'){
			$unit = "o";
			$attribute = "$table_pmax_dir";
			$query = "select a.$table_pmax_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Min Strain Direction'){
			$unit = "o";
			$attribute = "$table_pmin_dir";
			$query = "select a.$table_pmin_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$unit = "bars";
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
		$result["unit"] = $unit;
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
		$unit = "";
		if($component == 'GPS Latitude'){
			$unit = "o";
			$attribute = "dd_gps_lat";
			$query = "select a.dd_gps_nserr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Longtitude'){
			$unit = "o";
			$attribute = "dd_gps_lon";
			$query = "select a.dd_gps_ewerr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Elevation'){
			$unit = "m";
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
		$result["unit"] = $unit;
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
		$unit = "";
		if($component == 'GPS Displacement'){
			$unit = "mm";
			$attribute = "dd_gpv_dmag";
			$query = "select a.dd_gpv_dherr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Displ'){
			$unit = "mm";
			$attribute = "dd_gpv_N";
			$query = "select a.dd_gpv_dnerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Displ'){
			$unit = "mm";
			$attribute = "dd_gpv_E";
			$query = "select a.dd_gpv_deerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Displ'){
			$unit = "mm";
			$attribute = "dd_gpv_vert";
			$query = "select a.dd_gpv_dverr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Velocity'){
			$unit = "mm/yr";
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelNorthErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Velocity'){
			$unit = "mm/yr";
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelEastErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, $cc a.$attribute as value from $table as a where a.ds_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Velocity'){
			$unit = "mm/yr";
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
		$result["unit"] = $unit;
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

