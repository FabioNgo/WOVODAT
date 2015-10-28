<?php 
class HydrologyRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Hydrology' ";
		$db->query( $query);
		$stations = $db->getList();
		$result = self::getTimeSeriesList_hd($vd_id,$stations);
		return $result;
	}

	private static function getTimeSeriesList_hd( $vd_id, $stations ) {
		$result = array();
		global $db;
				
		$query="
			select distinct a.sta_id,a.sta_code as ds_code,concat('Water Temperature') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_temp IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Water Elevation') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_welev IS NOT NULL 	
			union
			select distinct a.sta_id,a.sta_code as ds_code,concat('Water Depth') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_wdepth IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Water Level Changes') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_dwelev IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Barometric Pressure') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_bp IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Spring Discharge Rate') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_sdisc IS NOT NULL 	
			union 
			select select distinct a.sta_id,a.sta_code as ds_code,concat('Precipitation') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_prec IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Water PH') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_ph IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Conductivity') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_cond IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Content of Compound') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_comp_content IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('Air Temperature') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_atemp IS NOT NULL 	
			union 
			select distinct a.sta_id,a.sta_code as ds_code,concat('TDS') as type from jjcn_sta as a, hd as b where a.type='Hydrology' and a.vd_id=$vd_id and a.sta_id=b.hs_id and b.hd_tds IS NOT NULL 	
			";

		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Hydrology" ,
					   'data_type' => "Hydrology",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					   

					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	public static function getStationData( $table, $code, $component ) {
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table ) 
			return call_user_func_array("self::getStationData_".$key, array($key, $code, $component) );
	} 

	public static function getStationData_hd( $table, $code, $component ) {
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
		if($component == 'Water Temperature'){
			$unit ="oC";
			$attribute = "hd_temp";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Elevation'){
			$unit ="m";
			$attribute = "hd_welev";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Depth'){
			$unit ="m";
			$attribute = "hd_wdepth";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Level Changes'){
			$unit ="m";
			$attribute = "hd_dwelev";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$unit ="mbar";
			$attribute = "hd_bp";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Spring Discharge Rate'){
			$unit ="L/s";
			$attribute = "hd_sdisc";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Precipitation'){
			$unit ="mm";
			$attribute = "hd_prec";
			$query = "select a.hd_tprec  as filter, a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water PH'){
			$style = "horizontalbar";
			$errorbar = true;
			$attribute = "hd_ph";
			$query = "select a.hd_ph_err as err,a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Conductivity'){
			$style = "horizontalbar";
			$errorbar = true;
			$attribute = "hd_cond";
			$query = "select a.hd_cond_err as err,a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Content of Compound'){

			$style = "horizontalbar";
			$errorbar = true;
			$attribute = "hd_comp_content";
			$query = "select a.hd_comp _units as unit, a.hd_comp_species  as filter,a.hd_comp_content_err as err,a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Air Temperature'){
			$unit ="oC";
			$attribute = "hd_atemp";
			$query = "select  a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'TDS'){
			$unit ="mg/L";
			$attribute = "hd_tds";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}
		$db->query($query, $id);

		$res = $db->getList();
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( "time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"])
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
}

HydrologyRepository::$infor = json_decode( file_get_contents("Hydrology.json", true) , true);