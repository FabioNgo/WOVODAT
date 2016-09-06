<?php
/**
 *	This class supports query the data from data table dd_gps 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_gpsManager extends DeformationTablesManager {
	
	protected function setColumnsName(){
		$result = array("dd_gps_lat","dd_gps_lon","dd_gps_elev","dd_gps_slope");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_gps";
	}
	protected function setDataType(){
		return "GPSPosition&Slope";
	} // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	protected function setStationID(){
		$result = array("ds_id","ds_id_ref1");
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
		$table = "dd_gps";
		$errorbar = true;
		$style = "dot";  
		if($component == 'GPS Latitude'){
			$unit = "o";
			$attribute = "dd_gps_lat";
			$query = "select a.dd_gps_nserr as err ,a.dd_gps_time as time,a.$attribute as value from $table as a where a.ds_id=%s and a.ds_id_ref1=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Longitude'){
			$unit = "o";
			$attribute = "dd_gps_lon";
			$query = "select a.dd_gps_ewerr as err ,a.dd_gps_time as time, $cc a.$attribute as value from $table as a where a.ds_id=%s and a.ds_id_ref1=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Elevation'){
			$unit = "m";
			$attribute = "dd_gps_elev";
			$query = "select a.dd_gps_verr as err ,a.dd_gps_time as time, a.$attribute as value from $table as a where a.ds_id=%s and a.ds_id_ref1=%s and a.$attribute IS NOT NULL";
		}else if($component == 'GPS Baseline/Slope'){
			$unit = "m";
			$attribute = "dd_gps_slope";
			$query = "select a.dd_gps_errslope as err,a.dd_gps_time as time,a.$attribute as value from $table as a where a.ds_id=%s and a.ds_id_ref1=%s and a.$attribute IS NOT NULL";
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
        return "GPS";
    }
}