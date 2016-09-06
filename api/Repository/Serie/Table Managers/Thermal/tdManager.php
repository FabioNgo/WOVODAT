<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class tdManager extends ThermalTablesManager {
	
	protected function setColumnsName(){
		$result = array("td_temp","td_flux","td_bkgg","td_tcond");
		return $result;
	}
	protected function setTableName(){
		return "es_td";
	}
	protected function setDataType(){
		return "Thermal";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ts_id","ts_id");
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
		$table = "td";
		$errorbar = true;
		$style = "dot";
		if($component == 'Temperature'){
			$attribute = "td_temp";
			$errorbar = true;
			$unit ="oC";
			$query = "select a.td_time as time, a.td_terr as err, a.$attribute as value  from $table as a where a.ts_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Heat Flux'){
			$attribute = "td_flux";
			$unit ="W/m2";
			$errorbar = true;  
			$query = "select a.td_time as time, a.td_ferr as err, a.$attribute as value  from $table as a where a.ts_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Gethermal Gradient'){
			$attribute = "td_bkgg";
			$unit ="oC/km";
			$query = "select a.td_time as time, a.$attribute as value  from $table as a where a.ts_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Thermal Conductivity'){
			$attribute = "td_tcond";
			$unit ="W/(m2*oC)";
			$query = "select a.td_time as time, a.td_ferr as err, a.$attribute as value  from $table as a where a.ts_id=%s and a.$attribute IS NOT NULL";
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
        return "Thermal";
    }
}