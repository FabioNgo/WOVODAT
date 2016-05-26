<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class medManager extends MeteoTablesManager {
	
	protected function setColumnsName(){
		$result = array("med_temp","med_stemp","med_bp","med_hd","med_prec","med_wind","med_wsmin","med_wsmax","med_wdir","med_clc");
		return $result;
	}
	protected function setTableName(){
		return "es_med";
	}
	
	protected function setDataType(){
		return "Meteo Data";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ms_id","ms_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code","sta_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "med";
		$errorbar = false;
		$style = "dot";
		if($component == 'Air Temperature'){
			$attribute = "med_temp";
			$unit ="oC";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Soil Temperature'){
			$attribute = "med_stemp";
			$unit ="oC";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$attribute = "med_bp";
			$unit ="Mbar";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Precipitation'){
			$attribute = "med_prec";
			$unit ="mm";
			$query = "select a.med_tprec  as filter, a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Humidity'){
			
			$unit = "%";
			$attribute = "med_hd";
			$query = "select a.med_ph_err as err,a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Wind Speed'){
			$unit ="m/s";
			
			$attribute = "med_wind";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Min Wind Speed'){
			
			$unit ="m/s";
			$attribute = "med_wsmin";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";

		}else if($component == 'Max Wind Speed'){
			
			$unit ="m/s";
			$attribute = "med_wsmax";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";

		}else if($component == 'Wind Direction'){
			$attribute = "med_wdir";
			$unit ="o";
			$query = "select  a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Cloud Coverage'){
			$attribute = "med_clc";
			$unit ="%";
			$query = "select a.med_time as time, a.$attribute as value  from $table as a where a.ms_id=%s and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 