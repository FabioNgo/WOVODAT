<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_tltManager extends DeformationTablesManager {
	
	protected function setColumnsName(){
		$result = array("dd_tlt1","dd_tlt2","dd_tlt_temp");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_tlt";
	}
	
	protected function setDataType(){
		return "ElectronicTilt";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id","ds_id");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("ds_code","ds_code");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "dd_tlt"; 
		$errorbar = true;
		$style = "dot";
		if($component == 'Radial/X-axis Tilt'){
			$unit = "urad";
			$attribute = "dd_tlt1";
			$query = "select a.dd_tlt_err1 as err ,a.dd_tlt_time as time, a.$attribute as value from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Tangential/Y-axis Tilt'){
			$unit = "urad";
			$attribute = "dd_tlt2";
			$query = "select a.dd_tlt_err2 as err ,a.dd_tlt_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Tilt Temperature'){
			$unit = "oC";
			$attribute = "dd_tlt_temp";
			$errorbar = false;
			$query = "select a.dd_tlt_time as time, a.$attribute as value from $table as a where  a.ds_id=%s and a.$attribute IS NOT NULL";
		}
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
        return "Tilt";
    }
}