<?php
/**
 *	This class supports query the data from data table dd_str 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_strManager extends DeformationTablesManager {
	
	protected function setColumnsName(){
		$result = array("dd_str_comp1","dd_str_comp2","dd_str_comp3","dd_str_comp4","dd_str_vdstr","dd_str_sstr_ax1","dd_str_sstr_ax2","dd_str_sstr_ax3","dd_str_azi_ax1","dd_str_azi_ax2","dd_str_azi_ax3","dd_str_pmax","dd_str_pmin","dd_str_pmax_dir","dd_str_pmin_dir","dd_str_bpres");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_str";
	}
	
	protected function setDataType(){
		return "Strain";
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
		$table = "dd_str";
		$errorbar = true;
		$style = "dot";
		if($component == 'Strain Component 1'){
			$unit = "ustrain";
			$attribute = "dd_str_comp1";
			$query = "select a.dd_str_err1 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Component 2'){
			$unit = "ustrain";
			$attribute = "dd_str_comp2";
			$query = "select a.dd_str_err2 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Component 3'){
			$unit = "ustrain";
			$attribute = "dd_str_comp3";
			$query = "select a.dd_str_err3 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Component 4'){
			$unit = "ustrain";
			$attribute = "dd_str_comp4";
			$query = "select a.dd_str_err4 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Volumetric Strain Change'){
			$unit = "ustrain";
			$attribute = "dd_str_vdstr";
			$query = "select a.dd_str_vdstr_err as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Shear Strain Axis 1'){
			$unit = "ustrain";
			$attribute = "dd_str_sstr_ax1";
			$query = "select a.dd_str_stderr1 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Shear Strain Axis 2'){
			$unit = "ustrain";
			$attribute = "dd_str_sstr_ax2";
			$query = "select a.dd_str_stderr2 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Shear Strain Axis 3'){
			$unit = "ustrain";
			$attribute = "dd_str_sstr_ax3";
			$query = "select a.dd_str_stderr3 as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Azimuth Axis 1'){
			$unit = "o";
			$attribute = "dd_str_azi_ax1";
			$errorbar = false;
			$query = "select a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Azimuth Axis 2'){
			$unit = "o";
			$attribute = "dd_str_azi_ax2";
			$errorbar = false;
			$query = "select a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Azimuth Axis 3'){
			$unit = "o";
			$attribute = "dd_str_azi_ax3";
			$errorbar = false;
			$query = "select a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Maximum Strain'){
			$unit = "ustrain";
			$attribute = "dd_str_pmax";
			$query = "select a.dd_str_pmaxerr as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Minimum Strain'){
			$unit = "ustrain";
			$attribute = "dd_str_pmin";
			$query = "select a.dd_str_pminerr as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}
		else if($component == 'Maximum Strain Direction'){
			$unit = "o";
			$attribute = "dd_str_pmax_dir";
			$query = "select a.dd_str_pmax_direrr as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Minimum Strain Direction'){
			$unit = "o";
			$attribute = "dd_str_pmin_dir";
			$query = "select a.dd_str_pmin_direrr as err ,a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$unit = "bars";
			$attribute = "dd_str_bpres";
			$errorbar = false;
			$query = "select a.dd_str_time as time, a.$attribute as value  from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
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
        return "Strain";
    }
}