<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class fd_eleManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("fd_ele_field","fd_ele_spot","fd_ele_ares","fd_ele_dres");
		return $result;
	}
	protected function setTableName(){
		return "es_fd_ele";
	}
	protected function setMonitoryType(){
		return "Fields";
	} // monitory type Deformation, Gas, ....
	protected function setDataType(){
		return "Electric Fields";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("fs_id1","fs_id2");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code","sta_code1");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "fd_ele";
		$errorbar = true;
		$style = "dot";
		if($component == 'Electric Fields'){
			$unit = "mV";
			$attribute = "fd_ele_field";
			$query = "select a.fd_ele_ferr as err ,a.fd_ele_time as time, a.$attribute as value  from $table as a where a.fs_id1=%s and a.fs_id2=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Self Potential'){
			$unit = "mV";
			$attribute = "fd_ele_spot";
			$query = "select a.fd_ele_spot_err as err ,a.fd_ele_time as time, a.$attribute as value  from $table as a where a.fs_id1=%s and a.fs_id2=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Apparent Resistivity'){
			$unit = "Omega";
			$attribute = "fd_ele_ares";
			$query = "select a.fd_ele_ares_err as err ,a.fd_ele_time as time, a.$attribute as value  from $table as a where a.fs_id1=%s and a.fs_id2=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Direct Resistivity'){
			$unit = "Omega";
			$attribute = "fd_ele_dres";
			$query = "select a.fd_ele_dres_err as err ,a.fd_ele_time as time, a.$attribute as value  from $table as a where a.fs_id1=%s and a.fs_id2=%s and a.$attribute IS NOT NULL";
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