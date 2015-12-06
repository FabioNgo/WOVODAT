<?php 
class FieldRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();

		global $db;
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Field' ";
		$db->query( $query);
		$stations = $db->getList();
		$result = array_merge($result,self::getTimeSeriesList_fd_gra($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_fd_ele($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_fd_mag($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_fd_mgv($vd_id,$stations));
		return $result;
	}

	private static function getTimeSeriesList_fd_ele( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("fd_ele_field","fd_ele_spot","fd_ele_ares","fd_ele_dres");
		$table_name = "es_fd_ele";
		$query = "select a.fs_id1,a.sta_code,a.fs_id2,a.sta_code1";
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
					$x = array('category' => "Field" ,
							   'data_type' => "Electric Fields",
							   'station_code1' => $serie["sta_code"],
							   'station_code2' => $serie["sta_code1"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id1' => $serie["fs_id1"],
							   'sta_id2' => $serie["fs_id2"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}
	private static function getTimeSeriesList_fd_gra( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("fd_gra_fstr");
		$table_name = "es_fd_gra";
		$query = "select a.fs_id,a.sta_code,a.fs_id_ref,a.sta_code1";
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
					$x = array('category' => "Field" ,
							   'data_type' => "Gravity Fields",
							   'station_code1' => $serie["sta_code"],
							   'station_code2' => $serie["sta_code1"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id1' => $serie["fs_id"],
							   'sta_id2' => $serie["fs_id_ref"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}
	private static function getTimeSeriesList_fd_mag( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("fd_mag_f");
		$table_name = "es_fd_mg";
		$query = "select a.fs_id,a.sta_code,a.fs_id_ref,a.sta_code1";
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
					$x = array('category' => "Field" ,
							   'data_type' => "Magnetic Fields",
							   'station_code1' => $serie["sta_code"],
							   'station_code2' => $serie["sta_code1"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id1' => $serie["fs_id"],
							   'sta_id2' => $serie["fs_id_ref"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}
	private static function getTimeSeriesList_fd_mgv( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("fd_mgv_dec","fd_mgv_incl");
		$table_name = "es_fd_mgv";
		$query = "select a.fs_id,a.sta_code";
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
					$x = array('category' => "Field" ,
							   'data_type' => "Magnetic Fields",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["fs_id"]
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}
	public static function getStationData( $table, $component, $ids ) {
		// var_dump(self::$infor);
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table )
			// echo($key);
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$ids) );
	} 

	public static function getStationData_fd_ele( $table, $component,$ids ) {

		$id1 = $ids["sta_id1"];
		$id2 = $ids["sta_id2"];
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
		$unit = "";
		if($component == 'Electric Fields'){
			$unit = "mV";
			$attribute = "fd_ele_field";
			$query = "select a.fd_ele_ferr as err ,a.fd_ele_time as time, a.$attribute as value $cc from $table as a where a.fs_id1=$id1 and a.fs_id2=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Self Potential'){
			$unit = "mV";
			$attribute = "fd_ele_spot";
			$query = "select a.fd_ele_spot_err as err ,a.fd_ele_time as time, a.$attribute as value $cc from $table as a where a.fs_id1=$id1 and a.fs_id2=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Apparent Resistivity'){
			$unit = "Omega";
			$attribute = "fd_ele_ares";
			$query = "select a.fd_ele_ares_err as err ,a.fd_ele_time as time, a.$attribute as value $cc from $table as a where a.fs_id1=$id1 and a.fs_id2=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Direct Resistivity'){
			$unit = "Omega";
			$attribute = "fd_ele_dres";
			$query = "select a.fd_ele_dres_err as err ,a.fd_ele_time as time, a.$attribute as value $cc from $table as a where a.fs_id1=$id1 and a.fs_id2=$id2 and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$db->query($query, $id1,$id2);
		
		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " "
						);
			
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = $row["err"];
				}else{
					$temp["error"] = 0;
				}
			}
			array_push($data, $temp );			
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}
	public static function getStationData_fd_gra( $table, $component,$ids ) {

		$id1 = $ids["sta_id1"];
		$id2 = $ids["sta_id2"];
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
		$unit = "";
		if($component == 'Gravity'){
			$unit = "Gal";
			$attribute = "fd_gra_fstr";
			$query = "select a.fd_gra_ferr as err ,a.fd_gra_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id1 and a.fs_id_ref=$id2 and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id1,$id2);
		
		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " "
						);
			
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = $row["err"];
				}else{
					$temp["error"] = 0;
				}
			}
			array_push($data, $temp );			
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}
	public static function getStationData_fd_mag( $table, $component,$ids ) {

		$id1 = $ids["sta_id1"];
		$id2 = $ids["sta_id2"];
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
		$unit = "";
		if($component == 'Magnetic'){
			$unit = "nT";
			$attribute = "fd_mag_f";
			$query = "select a.fd_mag_ferr as err ,a.fd_mag_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id1 and a.fs_id_ref=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic X'){
			$unit = "nT";
			$attribute = "fd_mag_compx";
			$query = "select a.fd_mag_errx as err ,a.fd_mag_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id1 and a.fs_id_ref=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Y'){
			$unit = "nT";
			$attribute = "fd_mag_compy";
			$query = "select a.fd_mag_erry as err ,a.fd_mag_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id1 and a.fs_id_ref=$id2 and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Z'){
			$unit = "nT";
			$attribute = "fd_mag_compz";
			$query = "select a.fd_mag_errz as err ,a.fd_mag_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id1 and a.fs_id_ref=$id2 and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id1,$id2);
		
		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " "
						);
			
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = $row["err"];
				}else{
					$temp["error"] = 0;
				}
			}
			array_push($data, $temp );			
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}
	public static function getStationData_fd_mgv( $table, $component,$ids ) {

		$id = $ids["sta_id"];
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = false;
		$data = array();
		$filter = "";
		$query = "";
		$unit = "";
		if($component == 'Magnetic Declination'){
			$unit = "o";
			$attribute = "fd_mgv_dec";
			$query = "select a.fd_mgv_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Inclination'){
			$unit = "o";
			$attribute = "fd_mgv_incl";
			$query = "select a.fd_mgv_time as time, a.$attribute as value $cc from $table as a where a.fs_id=$id and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id1,$id2);
		
		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " "
						);
			
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = $row["err"];
				}else{
					$temp["error"] = 0;
				}
			}
			array_push($data, $temp );			
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}
}

FieldRepository::$infor = json_decode( file_get_contents("Field.json", true) , true);

