<?php
/**
 *	This class supports query the data from data table gd
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class gdManager extends GasTablesManager {
	
	protected function setColumnsName(){
		$result = array("gd_gtemp","gd_bp","gd_flow","gd_concentration");
		return $result;
	}
	protected function setTableName(){
		return "es_gd";
	}  
	
	protected function setDataType(){
		return "Sampled Gas";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("gs_id","gs_id");
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
		$table = "gd";
		$errorbar = false;
		$style = "dot";
		if($component == 'Gas Temperature'){
			$unit = "oC";
			$attribute = "gd_gtemp";
			$query = "select a.gd_time as time, a.$attribute as value  from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Atmospheric Pressure'){
			$unit = "mbar";
			$attribute = "gd_bp";
			$query = "select a.gd_time as time, a.$attribute as value  from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Gas Concentration'){
			$unit = "oC";
			$attribute = "gd_concentration";
			$errorbar = true;
			$query = "select a.gd_species as filter, a.gd_time as time, a.gd_concentration_err as err, a.gd_units as unit, a.$attribute as value  from $table as a where  a.gs_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Gas Emission'){  
			$unit = "";
			$attribute = "gd_flow";
			$query = "select a.gd_time as time, a.$attribute as value  from $table as a where a.gs_id=%s and a.$attribute IS NOT NULL";
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
        return "Sampled Gas Events";
    }
}