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

		

		$result = self::getTimeSeriesList_gd_sol($vd_id,$stations);

		$result = array_merge($result,self::getTimeSeriesList_gd_plu($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_gd($vd_id,$stations));
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
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Total Gas Flux') as type from jjcn_sta as a, gd_sol as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_sol_tflux IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Highest Gas Flux') as type from jjcn_sta as a, gd_sol as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_sol_high IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Highest Temperature') as type from jjcn_sta as a, gd_sol as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_sol_htemp IS NOT NULL 			
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Gas" ,
					   'data_type' => "SoilEfflux",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
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
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Plume Height') as type from jjcn_sta as a, gd_plu as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_plu_height IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Gas Emission Rate') as type from jjcn_sta as a, gd_plu as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_plu_emit IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Gas Emission Mass') as type from jjcn_sta as a, gd_plu as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.cs_id and b.gd_plu_mass IS NOT NULL 			
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Total Gas Emission') as type from jjcn_sta as a, gd_plu as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_plu_etot IS NOT NULL 			
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Gas" ,
					   'data_type' => "GasPlume",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
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
				

		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Gas Temperature') as type from jjcn_sta as a, gd as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_gtemp IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Atmospheric Pressure') as type from jjcn_sta as a, gd as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_bp IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Gas Emission') as type from jjcn_sta as a, gd as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_flow IS NOT NULL 			
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Gas Concentration') as type from jjcn_sta as a, gd as b where a.type='Gas' and a.vd_id=%d and a.sta_id=b.gs_id and b.gd_concentration IS NOT NULL 			
			";



		$db->query( $query, $vd_id,$vd_id,$vd_id,$vd_id);
		
		$serie_list = $db->getList();
		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Gas" ,
					   'data_type' => "SampledGas",
					   'station_code' => $serie["ds_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"] 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		// var_dump($result);
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
	public static function getStationData( $table, $code, $component,$id ) {
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$id) );
	}

	public static function getStationData_gd( $table, $component,$id) {
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
		if($component == 'Gas Temperature'){
			$attribute = "gd_gtemp";
			$errorbar = false;
			$query = "select a.gd_time as time, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
	
		}else if($component == 'Atmospheric Pressure'){
			$attribute = "gd_bp";
			$errorbar = false;
			$query = "select a.gd_time as time, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Gas Emission'){
			$attribute = "gd_flow";
			$query = "select a.gd_time as time, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Gas Concentration'){
			$attribute = "gd_concentration";
			$query = "select a.gd_species as filter, a.gd_concentration_err as err, a.gd_time as time, a.$attribute as value from $table as a where a.gs_id=$id and a.$attribute IS NOT NULL";
			// echo($query);
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
			if($errorbar){
				$temp["error"] = $row["err"];
			}
			array_push($data, $temp );			
		}
		// echo("Asd");
		// var_dump($data);
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
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
			if ($filter != ""){
				$temp["filter"] = $row[$filter];
			}else{
				$temp["filter"] = " ";
			}
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

GasRepository::$infor = json_decode( file_get_contents("Gas.json", true) , true);
