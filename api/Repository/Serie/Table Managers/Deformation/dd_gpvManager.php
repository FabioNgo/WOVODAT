<?php
/**
 *	This class supports query the data from data table dd_gpv 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_gpvManager extends DeformationTablesManager {
	
	protected function setColumnsName(){
		$result = array("dd_gpv_dmag","dd_gpv_daz","dd_gpv_vincl","dd_gpv_N","dd_gpv_E","dd_gpv_vert","dd_gpv_staVelNorth","dd_gpv_staVelEast",
		"dd_gpv_staVelVert");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_gpv";
	}
	
	protected function setDataType(){
		return "GPSVector";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id","ds_id");
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
		$table = "dd_gpv";
		$errorbar = true;
		$style = "horizontalbar";
		if($component == 'GPS Displacement'){
			$unit = "mm";
			$attribute = "dd_gpv_dmag";
			$query = "select a.dd_gpv_dherr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Displ.'){
			$unit = "mm";
			$attribute = "dd_gpv_N";
			$query = "select a.dd_gpv_dnerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Displ.'){
			$unit = "mm";
			$attribute = "dd_gpv_E";
			$query = "select a.dd_gpv_deerr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime, a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Displ.'){
			$unit = "mm";
			$attribute = "dd_gpv_vert";
			$query = "select a.dd_gpv_dverr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS N-S Velocity'){
			$unit = "mm/yr";
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelNorthErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS E-W Velocity'){
			$unit = "mm/yr";
			$attribute = "dd_gpv_staVelNorth";
			$query = "select a.dd_gpv_staVelEastErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Vertical Velocity'){
			$unit = "mm/yr";
			$attribute = "dd_gpv_staVelVert";
			$query = "select a.dd_gpv_staVelVertErr as err ,a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Displ-azimuth'){
			$style = "dot";
			$unit = "o";
			$attribute = "dd_gpv_daz";
			$query = "select a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Displ-inclination'){
			$style = "dot";
			$unit = "o";
			$attribute = "dd_gpv_vincl";
			$query = "select a.dd_gpv_stime as stime,a.dd_gpv_etime as etime,a.$attribute as value   from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 