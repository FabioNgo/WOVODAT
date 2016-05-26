<?php
/**
 *	This class supports query the data from data table fd_mag 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class fd_magManager extends FieldsTablesManager {
	
	protected function setColumnsName(){
		$result = array("fd_mag_f");
		return $result;
	}
	protected function setTableName(){
		return "es_fd_mag";
	}
	
	protected function setDataType(){
		return "Magnetic Fields";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("fs_id","fs_id_ref");
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
		$table = "fd_mag";
		$errorbar = true;
		$style = "dot";
		if($component == 'Magnetic'){
			$unit = "nT";
			$attribute = "fd_mag_f";
			$query = "select a.fd_mag_ferr as err ,a.fd_mag_time as time, a.$attribute as value from $table as a where a.fs_id=%s and a.fs_id_ref=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic X'){
			$unit = "nT";
			$attribute = "fd_mag_compx";
			$query = "select a.fd_mag_errx as err ,a.fd_mag_time as time, a.$attribute as value from $table as a where a.fs_id=%s and a.fs_id_ref=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Y'){
			$unit = "nT";
			$attribute = "fd_mag_compy";
			$query = "select a.fd_mag_erry as err ,a.fd_mag_time as time, a.$attribute as value from $table as a where a.fs_id=%s and a.fs_id_ref=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Magnetic Z'){
			$unit = "nT";
			$attribute = "fd_mag_compz";
			$query = "select a.fd_mag_errz as err ,a.fd_mag_time as time, a.$attribute as value from $table as a where a.fs_id=%s and a.fs_id_ref=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 