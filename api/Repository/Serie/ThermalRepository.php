<?php 
class ThermalRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Thermal' ";
		$db->query( $query);
		$stations = $db->getList();
		$result = self::getTimeSeriesList_td($vd_id,$stations);
		return $result;
	}

	private static function getTimeSeriesList_td( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("td_temp","td_flux","td_bkgg","td_tcond");
		$table_name = "es_td";
		$query = "select a.ts_id,a.sta_code";
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
					$x = array('category' => "Thermal" ,
							   'data_type' => "Thermal",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["ts_id"],
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

	public static function getStationData_td( $table, $component,$ids ) {
		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "horizontalbar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$query = "";
		// echo("a");
		$unit = "";
		if($component == 'Temperature'){
			$attribute = "td_temp";
			$style = "horizontalbar";
			$errorbar = true;
			$unit ="oC";
			$query = "select a.td_time as time, a.td_terr as err, a.$attribute as value $cc from $table as a where a.ts_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Heat Flux'){
			$attribute = "td_flux";
			$style = "horizontalbar";
			$unit ="W/m2";
			$errorbar = true;
			$query = "select a.td_time as time, a.td_ferr as err, a.$attribute as value $cc from $table as a where a.ts_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Gethermal Gradient'){
			$attribute = "td_bkgg";
			$style = "dot";
			$unit ="oC/km";
			$query = "select a.td_time as time, a.$attribute as value $cc from $table as a where a.ts_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Thermal Conductivity'){
			$attribute = "td_tcond";
			$style = "dot";
			$unit ="W/(m2*oC)";
			$query = "select a.td_time as time, a.td_ferr as err, a.$attribute as value $cc from $table as a where a.ts_id=$id and a.$attribute IS NOT NULL";
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

ThermalRepository::$infor = json_decode( file_get_contents("Thermal.json", true) , true);