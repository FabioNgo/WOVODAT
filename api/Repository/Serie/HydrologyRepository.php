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
		$cols_name = array("hd_temp","hd_wdepth","hd_bp","hd_sdisc","hd_prec","hd_ph","hd_cond","hd_comp_content","hd_atemp","hd_tds");
		$table_name = "es_hd";
		$query = "select a.hs_id,a.sta_code";
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
					$x = array('category' => "Hydrology" ,
							   'data_type' => "Hydrology",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["hs_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	public static function getStationData( $table, $component, $ids ) {
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table )
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$ids) );
	}

	public static function getStationData_hd( $table, $component,$ids ) {
		global $db;
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
		// echo("a");
		$unit = "";
		if($component == 'Water Temperature'){
			$attribute = "hd_temp";
			$unit ="oC";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Elevation'){
			$attribute = "hd_welev";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Depth'){
			$attribute = "hd_wdepth";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water Level Changes'){
			$attribute = "hd_dwelev";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$attribute = "hd_bp";
			$unit ="mbar";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Spring Discharge Rate'){
			$attribute = "hd_sdisc";
			$unit ="L/s";
			$query = "select a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Precipitation'){
			$attribute = "hd_prec";
			$unit ="mm";
			$query = "select a.hd_tprec  as filter, a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Water ph'){
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
			$query = "select a.hd_comp_species  as filter,a.hd_comp_units as unit,a.hd_comp_content_err as err,a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";

		}else if($component == 'Air Temperature'){
			$attribute = "hd_atemp";
			$unit ="oC";
			$query = "select  a.hd_time as time, a.$attribute as value $cc from $table as a where a.hs_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'TDS'){
			$attribute = "hd_tds";
			$unit ="mg/L";
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
			if($errorbar){
				if($row["err"] == null){
					$temp["error"] = 0;
				}else{
					$temp["error"] = $row["err"];
				}
				
			}
			if(array_key_exists("unit", $row)){
				$unit = $row["unit"];
				if($unit==null){
					$unit = "";
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

HydrologyRepository::$infor = json_decode( file_get_contents("Hydrology.json", true) , true);