<?php
/**
 *	This class supports query the data from data table dd_tlv 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_tlvManager extends DeformationTablesManager {
	
	protected function setColumnsName(){
		$result = array("dd_tlv_mag","dd_tlv_azi");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_tlv";
	}
	protected function setDataType(){
		return "TitltVector";
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
		$table = "dd_tlv";
		$errorbar = true;
		$style = "horizontalbar";
		if($component == 'Tilt Vector'){    
			$unit = "urad";
			$attribute = "dd_tlv_mag";
			$query = "select a.dd_tlv_magerr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, a.$attribute as value from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		} else if($component == 'Tilt Azimuth'){
			$unit = "o";
			$attribute = "dd_tlv_azi";
			$query = "select a.dd_tlv_azierr as err ,a.dd_tlv_stime as stime, a.dd_tlv_etime as etime, a.$attribute as value from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";

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
        return "Tilt Vector";
    }
}