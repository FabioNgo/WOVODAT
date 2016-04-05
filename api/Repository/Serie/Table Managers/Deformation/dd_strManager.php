<?php
/**
 *	This class supports query the data from data table dd_tlt 
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
class dd_strManager extends TableManager {
	
	protected function setColumnsName(){
		$result = array("dd_str_comp1","dd_str_comp2","dd_str_comp3","dd_str_comp4","dd_str_vdstr","dd_str_sstr_ax1","dd_str_sstr_ax2",
					"dd_str_sstr_ax3","dd_str_azi_ax1","dd_str_azi_ax2","dd_str_azi_ax3","dd_str_pmax","dd_str_pmin");
		return $result;
	}
	protected function setTableName(){
		return "es_dd_str";
	}
	protected function setMonitoryType(){
		return "Deformation";
	} // monitory type Deformation, Gas, ....
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
		if($component == 'Strain Comp-1'){
			$unit = "ustrain";
			$attribute = "$table_comp1";
			$query = "select a.$table_err1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-2'){
			$unit = "ustrain";
			$attribute = "$table_comp2";
			$query = "select a.$table_err2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-3'){
			$unit = "ustrain";
			$attribute = "$table_comp3";
			$query = "select a.$table_err3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain Comp-4'){
			$unit = "ustrain";
			$attribute = "$table_comp4";
			$query = "select a.$table_err4 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Volumetric Strain change'){
			$unit = "ustrain";
			$attribute = "$table_vdstr";
			$query = "select a.$table_vdstr_err as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-1'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax1";
			$query = "select a.$table_stderr1 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-2'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax2";
			$query = "select a.$table_stderr2 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Shear strain axis-3'){
			$unit = "ustrain";
			$attribute = "$table_sstr_ax3";
			$query = "select a.$table_stderr3 as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-1'){
			$unit = "o";
			$attribute = "$table_azi_ax1";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-2'){
			$unit = "o";
			$attribute = "$table_azi_ax2";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Strain azimuth axis-3'){
			$unit = "o";
			$attribute = "$table_azi_ax3";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
			$unit = "ustrain";
			$attribute = "$table_pmax";
			$query = "select a.$table_pmaxerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain'){
			$unit = "ustrain";
			$attribute = "$table_pmin";
			$query = "select a.$table_pminerr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Max Strain Direction'){
			$unit = "o";
			$attribute = "$table_pmax_dir";
			$query = "select a.$table_pmax_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Min Strain Direction'){
			$unit = "o";
			$attribute = "$table_pmin_dir";
			$query = "select a.$table_pmin_direrr as err ,a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}else if($component == 'Barometric Pressure'){
			$unit = "bars";
			$attribute = "$table_bpres";
			$errorbar = false;
			$query = "select a.$table_time as time, a.$attribute as value $cc from $table as a where a.ds_id=%s and a.$attribute IS NOT NULL";
		}
		$result = array("unit" => $unit,
						"style" => $style,
						"errorbar" => $errorbar,
						"query" =>$query
						);
		return $result;
	} // params to get data station [unit,flot_style,errorbar,query]
} 