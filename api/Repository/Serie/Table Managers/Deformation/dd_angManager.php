<?php
/**
 *	This class supports query the data from data table dd_ang 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_angManager extends DeformationTablesManager {

	protected function setColumnsName(){
		$result = array("dd_ang_hort1","dd_ang_hort2","dd_ang_vert1","dd_ang_vert2");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_ang";
	}
	protected function setDataType(){
		return "Angle";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id1","ds_id2");
		return $result;
	} // column names represent stationID1,station ID2
	protected function setStationCode(){
		$result = array("sta_code1","sta_code2");
		return $result;
	} // column name represent primary stationCode1, stationCode2.
	protected function setStationDataParams($component){
		$unit="";
		$attribute = "";
		$query = "";
		$table = "dd_ang";
		$errorbar = true;
		$style = "dot";
		if($component == 'Horizontal Angle Target-1'){
			$unit = "o";
			$attribute = "dd_ang_hort1";
			$query = "select a.ds_id2 as filter, a.dd_ang_herr1 as err ,a.dd_ang_time as time, a.$attribute as value  from $table as a where a.ds_id1=$id1 and a.$attribute IS NOT NULL";
		}
		else if($component == 'Horizontal Angle Target-2'){
			$unit = "o";
			$attribute = "dd_ang_hort2";
			$query = "select a.ds_id2 as filter,a.dd_ang_herr2 as err ,a.dd_ang_time as time, a.$attribute as value  from $table as a where a.ds_id1=$id1 and a.$attribute IS NOT NULL";
		}
		else if($component == 'Vertical Angle Target-1'){
			$unit = "o";
			$attribute = "dd_ang_vert1";
			$query = "select a.ds_id2 as filter,a.dd_ang_verr1 as err ,a.dd_ang_time as time, a.$attribute as value  from $table as a where a.ds_id1=$id1  and a.$attribute IS NOT NULL";
		}
		else if($component == 'Vertical Angle Target-2'){
			$unit = "o";
			$attribute = "dd_ang_vert2";
			$query = "select a.ds_id2 as filter,a.dd_ang_verr2 as err ,a.dd_ang_time as time, a.$attribute as value  from $table as a where a.ds_id1=$id1  and a.$attribute IS NOT NULL";
		}
		else if($component == 'Horizontal Angle Target-2'){
			$unit = "o";
			$attribute = "dd_ang_vert1";
			$query = "select a.ds_id2 as filter,a.dd_ang_verr1 as err ,a.dd_ang_time as time, a.$attribute as value  from $table as a where a.ds_id1=$id1 and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 