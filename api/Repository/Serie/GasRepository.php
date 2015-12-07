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

		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Gas'";
		$db->query( $query);
		$stations = $db->getList();

		

		$result = self::getTimeSeriesList_gd_sol($vd_id,$stations);
		$result = array_merge($result,self::getTimeSeriesList_gd_plu($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_gd($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_gd_sol($vd_id,$stations));
		// var_dump($result);
		return $result;
	}

	/**
	*	@param volcanoId
	* @return List of time serie from Gd_sol table
	*/
	private static function getTimeSeriesList_gd_sol( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("gd_sol_tflux","gd_sol_high","gd_sol_htemp");
		$table_name = "es_gd_sol";
		$query = "select a.gs_id,a.sta_code";
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
					$x = array('category' => "Gas" ,
							   'data_type' => "SoilEfflux",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["gs_id"],
							   );
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
		$cols_name = array("gd_plu_height","gd_plu_mass","gd_plu_etot"," gd_plu_emit");
		$table_name = "es_gd_plu";
		$query = "select a.gs_id,a.gs_code,a.cs_id,a.cs_code";
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
					$x = array('category' => "Gas" ,
							   'data_type' => "Gas Plume",
							   'station_code1' => $serie["gs_code"],
							   'station_code2' => $serie["cs_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id1' => $serie["gs_id"],
							   'sta_id2' => $serie["cs_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code1"].$x["station_code2"].$x["component"] );
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
		$cols_name = array("gd_gtemp","gd_bp","gd_flow","gd_concentration");
		$table_name = "es_gd";
		$query = "select a.gs_id,a.sta_code";
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

					$x = array('category' => "Gas" ,
							   'data_type' => "Sampled Gas",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["gs_id"],
							   );
					// var_dump($x);
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
			
			
		}	
				
		return $result;
	}

	
	public static function getStationData( $table, $component, $ids ) {
		// echo($table);
		// var_dump(self::$infor);
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table )
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$ids) );
	} 

	public static function getStationData_gd( $table, $component,$ids) {
		
		global $db;
		// echo($ids);
		$id = $ids["sta_id"];
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
		if($component == 'Gas Temperature'){
			$unit = "oC";
			$attribute = "gd_gtemp";
			$query = "select a.gd_time as time, a.$attribute as value $cc from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}
		else if($component == 'Atmospheric Pressure'){
			$unit = "mbar";
			$attribute = "gd_bp";
			$query = "select a.gd_time as time, a.$attribute as value $cc from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}
		else if($component == 'Gas Concentration'){
			$unit = "oC";
			$attribute = "gd_concentration";
			$style = "horizontalbar";
			$errorbar = true;
			$query = "select a.gd_time as time, a.gd_concentration_err as err, a.gd_units as unit, a.$attribute as value $cc from $table as a where  a.gs_id=$id and a.$attribute IS NOT NULL";
		}
		else if($component == 'Gas Emission Rate'){
			$unit = "";
			$attribute = "gd_flow";
			$query = "select a.gd_time as time, a.$attribute as value $cc from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
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
			if(array_key_exists("unit", $row)){
				$unit = $row["unit"];
				if($unit==null){
					$unit = "";
				}
			}
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

	public static function getStationData_gd_plu($table, $component,$ids ) {
		// echo("As");
		global $db;
		$id1 = $ids["sta_id1"];
		$id2 = $ids["sta_id2"];
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
		if($component == 'Plume Height'){
			$unit = "km";
			$attribute = "gd_plu_height";
			$errorbar = false;
			$query = "select a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=$id1 or a.cs_id=$id2)  and a.$attribute IS NOT NULL";
	
		}else if($component == 'Gas Emission Rate'){

			$attribute = "gd_plu_emit";
			$errorbar = true;
			$style = 'horizontalbar';
			$query = "select a.gd_plu_units as unit, a.gd_plu_species as filter, a.gd_plu_emit_err as err, a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=$id1 or a.cs_id=$id2) and a.$attribute IS NOT NULL";
		}else if($component == 'Gas Emission Mass'){

			$attribute = "gd_plu_etot";
			$errorbar = true;
			$style = 'horizontalbar';
			$query = "select a.gd_plu_units as unit, a.gd_plu_species as filter, a.gd_plu_etot_err as err, a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=$id1 or a.cs_id=$id2) and a.$attribute IS NOT NULL";
			
		}
		// echo($query);
		$db->query($query);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
						);
			
			if(array_key_exists("filter", $row)){
				$temp["filter"] = $row["filter"];
			}else{
				$temp["filter"] = " ";
			}
			if(array_key_exists("unit", $row)){
				$unit = $row["unit"];
			}
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

	public static function getStationData_gd_sol( $table, $component,$ids ) {
		global $db;
		$id =$ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "dot";
		$errorbar = true;
		$data = array();
		$filter = "";
		$query = "";
		$unit ="";
		if($component == 'Total Gas Flux'){

			$attribute = "gd_sol_tflux";
			$errorbar = true;
			$style = 'horizontalbar';
			$query = "select a.gd_sol_units as unit, a.gd_sol_species as filter, a.gd_sol_time as time,  a.gd_sol_tflux_err as err, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
	
		}else if($component == 'Highest Gas Flux'){
			$unit ="g/m2/d";
			$attribute = "gd_sol_high";
			$query = "select  a.gd_sol_species as filter, a.gd_sol_time as time, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Highest Temperature'){
			$unit ="oC";
			$attribute = "gd_sol_htemp";
			$query = "select a.gd_sol_time as time, a.gd_sol_species as filter, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}

		$db->query($query);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
						);
			
			if(array_key_exists("filter", $row)){
				$temp["filter"] = $row["filter"];
			}else{
				$temp["filter"] = " ";
			}
			if(array_key_exists("unit", $row)){
				$unit = $row["unit"];
			}
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

GasRepository::$infor = json_decode( file_get_contents("Gas.json", true) , true);
