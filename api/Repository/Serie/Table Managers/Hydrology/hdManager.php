<?php
/**
 *	This class supports query the data from data table hd 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class hdManager extends HydrologyTablesManager {
	
	protected function setColumnsName(){
		$result = array("hd_temp","hd_welev","hd_wdepth","hd_dwlev","hd_bp","hd_sdisc","hd_prec","hd_ph","hd_cond","hd_comp_content","hd_atemp","hd_tds");
		return $result;
	}
	protected function setTableName(){
		return "es_hd";
	}
	
	protected function setDataType(){
		return "Hydrology";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("hs_id","hs_id");
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
		$table = "hd";
		$errorbar = false;
		$style = "dot";
		if($component == 'Water Temperature'){
			$attribute = "hd_temp";
			$unit ="oC";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Water Elevation'){
			$attribute = "hd_welev";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Water Depth'){
			$attribute = "hd_wdepth";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Water Level Changes'){
			$attribute = "hd_dwlev";
			$unit ="m";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$attribute = "hd_bp";
			$unit ="mbar";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Spring Discharge Rate'){
			$attribute = "hd_sdisc";
			$unit ="L/s";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Precipitation'){
			$attribute = "hd_prec";
			$unit ="mm";
			$query = "select a.hd_tprec  as filter, a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Water PH'){
			$errorbar = true;
			$attribute = "hd_ph";
			$query = "select a.hd_ph_err as err,a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Conductivity'){
			$errorbar = true;  
			$attribute = "hd_cond";
			$query = "select a.hd_cond_err as err,a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Content Of Compound'){
			$errorbar = true;
			$attribute = "hd_comp_content";
			$query = "select a.hd_comp_species  as filter,a.hd_comp_units as unit,a.hd_comp_content_err as err,a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";

		}else if($component == 'Air Temperature'){
			$attribute = "hd_atemp";
			$unit ="oC";
			$query = "select  a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'TDS'){
			$attribute = "hd_tds";
			$unit ="mg/L";
			$query = "select a.hd_time as time, a.$attribute as value  from $table as a where a.hs_id=%s and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]

    protected function setShortDataType()
    {
        // TODO: Implement setShortDataType() method.
        return "Hydrologic";
    }
}