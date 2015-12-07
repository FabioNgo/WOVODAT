<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class gd_pluManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("gd_plu_height","gd_plu_mass","gd_plu_etot"," gd_plu_emit");
		return $result;
	}
	protected function setTableName(){
		return "es_gd_plu";
	}
	protected function setMonitoryType(){
		return "Gas";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "Gas Plume";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("gs_id","cs_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("gs_code","cs_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "gd_sol";
		$errorbar = false;
		$style = "dot";
		if($component == 'Plume Height'){
			$unit = "km";
			$attribute = "gd_plu_height";
			$errorbar = false;
			$query = "select a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=%s or a.cs_id=%s)  and a.$attribute IS NOT NULL";
	
		}else if($component == 'Gas Emission Rate'){

			$attribute = "gd_plu_emit";
			$errorbar = true;
			$style = 'horizontalbar';
			$query = "select a.gd_plu_units as unit, a.gd_plu_species as filter, a.gd_plu_emit_err as err, a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=%s or a.cs_id=%s) and a.$attribute IS NOT NULL";
		}else if($component == 'Gas Emission Mass'){

			$attribute = "gd_plu_etot";
			$errorbar = true;
			$style = 'horizontalbar';
			$query = "select a.gd_plu_units as unit, a.gd_plu_species as filter, a.gd_plu_etot_err as err, a.gd_plu_time as time, a.$attribute as value from $table as a where (a.gs_id=%s or a.cs_id=%s) and a.$attribute IS NOT NULL";
			
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