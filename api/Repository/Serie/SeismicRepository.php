<?php 

class SeismicRepository {
	public static $infor;

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		global $db;
		$query = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = $vd_id AND a.type='Seismic'";
		$db->query( $query);
		$stations = $db->getList();

		$result = self::getTimeSeriesList_sd_evs($vd_id,$stations);

		$result = array_merge($result,self::getTimeSeriesList_sd_evn($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_ivl($vd_id,$stations));
		// $result = array_merge($result,self::getTimeSeriesList_sd_int($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_rsm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_ssm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_trm($vd_id,$stations));

		return $result;
	}

	private static function getTimeSeriesList_sd_evs( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("sd_evn_edep","sd_evn_pmag");
		$table_name = "es_sd_evn";
		$query = "select a.n_id,a.n_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "SeismicEventFromSingleStation",
							   'station_code' => $serie["n_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["n_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_sd_int( $vd_id, $stations ) {
		$result = array();
		global $db;
				
		$query="
			select distinct a.sta_id,a.sta_code as ss_code,concat('Max Distance Felt') as type from jjcn_sta as a, sd_int as b 
			where a.type='Seismic' and a.vd_id=$vd_id and b.sd_int_maxdist IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Max Intensity') as type from jjcn_sta as a, sd_int as b where a.type='Seismic' and a.vd_id=$vd_id and b.sd_int_maxrint IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Distance at Max-intensity') as type from jjcn_sta as a, sd_int as b where a.type='Seismic' and a.vd_id=$vd_id and b.sd_int_maxrint_dist IS NOT NULL 
			
			";

		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicIntensity",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'ds_id' => $serie["sta_id"],
					   
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_trm( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("sd_trm_domfreq1","sd_trm_domfreq2","sd_trm_maxamp","sd_trm_reddis");
		$table_name = "es_sd_trm";
		$query = "select a.ss_id,a.sta_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "SeismicTremor",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["ss_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_sd_ivl( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array(	"sd_ivl_spint","sd_ivl_hdist","sd_ivl_avgdepth","sd_ivl_vdispers",
							"sd_ivl_hmigr_hyp","sd_ivl_vmigr_hyp","sd_ivl_nrec",
							"sd_ivl_nfelt","sd_ivl_etot","sd_ivl_fmin","sd_ivl_fmax","sd_ivl_amin","sd_ivl_amax"
							);
		$table_name = "es_sd_ivl";
		$query = "select a.ss_id,a.sta_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "SeismicInterval",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["ss_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_sd_rsm( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("sd_rsm_count");
		$table_name = "es_sd_rsm";
		$query = "select a.ss_id,a.sta_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "RSAM",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["ss_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_sd_ssm( $vd_id, $stations ) {
		$result = array();
		global $db;

		$cols_name = array("sd_ssm_lowf","sd_ssm_highf","sd_ssm_count");
		$table_name = "es_sd_ssm";
		$query = "select a.ss_id,a.sta_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "SSAM",
							   'station_code' => $serie["sta_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["ss_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	private static function getTimeSeriesList_sd_evn( $vd_id, $stations ) {
		$result = array();
		global $db;
		$cols_name = array("sd_evn_edep","sd_evn_pmag");
		$table_name = "es_sd_evn";
		$query = "select a.n_id,a.n_code";
		for($i =0;$i<sizeof($cols_name);$i++){
			$query = $query.",a.".$cols_name[$i];
		}
		$query = $query." from $table_name as a where a.vd_id=$vd_id";
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list)  ; $i++) { 
			$serie = $serie_list[$i];
			for($j =0;$j<sizeof($cols_name);$j++){
				if($serie[$cols_name[$j]]!=""){
					$x = array('category' => "Seismic" ,
							   'data_type' => "SeismicEventFromNetwork",
							   'station_code' => $serie["n_code"],
							   'component' => $serie[$cols_name[$j]],
							   'sta_id' => $serie["n_id"],
							   );
					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 					array_push($result,  $x );
				}
			}
				
			
		}	
				
		return $result;
	}

	public static function getStationData( $table, $component, $ids ) {
		// echo($table);
		foreach (self::$infor as $key => $type) if ( $type["data_type"] == $table )
			return call_user_func_array("self::getStationData_".$key, array( $key, $component,$ids) );
	} 
	private static function getStationData_sd_evn( $table, $component,$ids ) {
		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "";
		$errorbar = false;
		$data = array();
		$unit = "";
		$filter = "";
		$query="";
		if($component == 'Earthquake Depth'){
			$unit = "km";
			$style = "circle";
			$errorbar = true;
			$attribute = "sd_evn_edep";
			$query = "select a.sd_evn_eqtype  as filter, a.sd_evn_derr as err ,a.sd_evn_time as time, a.$attribute as value $cc from $table  as a where a.sn_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Magnitude'){
			$style = "circle";
			$errorbar = false;
			$attribute = "sd_evn_pmag";
			$query = "select a.sd_evn_eqtype  as filter ,a.sd_evn_time as time, a.$attribute as value $cc from $table  as a where a.sn_id=$id and a.$attribute IS NOT NULL";
		}
			// echo($query);
		$db->query($query);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( 	"time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							
						);
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = $row["err"];
				}else{
					$temp["error"] = 0;
				}
			}
			if($row["filter"]!=null){
				$temp["filter"] = $row["filter"];
			}else{
				$temp["error"] = " ";
			}
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}
	public static function getStationData_sd_evs( $table, $component,$ids ) {
		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$unit = "";
		$query="";
		if($component == 'S-P Arrival Time'){
			$unit = "s";
			$attribute = "sd_evs_spint";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Epicenter From Event'){
			$unit = "km";
			$attribute = "sd_evs_dist_actven";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-Amplitude'){
			$unit = "cm";
			$attribute = "sd_evs_maxamptrac";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Dominant Frequency'){
			$unit = "Hz";
			$attribute = "sd_evs_domFre";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Magnitude'){
			$unit = "Hz";
			$attribute = "sd_evs_mag";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Energy'){
			$unit = "Erg";
			$attribute = "sd_evs_energy";
			$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}
		// echo($query);
		$db->query($query);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( 	"time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => $row["filter"],
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}

	public static function getStationData_sd_int( $table, $component,$id) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$unit = "";
		$query="";
		if($component == 'Max Distance Felt'){
			$unit = "km";
			$attribute = "sd_int_maxdist";
			$query = "select a.sd_int_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max-intensity'){
			$unit = "";
			$attribute = "sd_int_maxrint";
			$query = "select a.sd_int_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Distance at Max-intensity'){
			$unit = "km";
			$attribute = "sd_int_maxrint";
			$query = "select a.sd_int_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}

		$db->query($query);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( 	"time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => " ",
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}

	public static function getStationData_sd_rsm( $table, $component,$ids ) {

		global $db;
		$id = "";
		$sam_id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$unit ="";
		$query = "select a.sd_sam_id from sd_rsm as a";
		$db->query($query);
		$rsm_ids = $db->getList();
		foreach ($rsm_ids as $rsm_id) {
			$id = $rsm_id["sd_sam_id"];
		}
		if($component == 'RSAM Count'){
			$unit = "counts";
			$attribute = "sd_rsm_count";
			$query = "select a.sd_rsm_stime as stime, a.$attribute as value from $table  as a where a.sd_sam_id=$id and a.$attribute IS NOT NULL";
		}
			// echo($query);
		$db->query($query,$id);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			// $etime = date_timestamp_get();
			$temp = array( 	"stime" => floatval(1000 * $stime) ,
							// "etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"filter" => " ",
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}

	public static function getStationData_sd_ssm( $table, $component,$ids ) {
		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$unit = "";
		$query="";
		if($component == 'SSAM Low-freq Limit'){
			$unit = "Hz";
			$attribute = "sd_ssm_lowf";
			$query = "select a.sd_ssm_stime as stime,a.sd_ssm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'SSAM Hight-freq Limit'){
			$unit = "Hz";
			$attribute = "sd_ssm_highf";
			$query = "select a.sd_ssm_stime as stime,a.sd_ssm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'SSAM Counts'){
			$unit = "counts";
			$attribute = "sd_ssm_count";
			$query = "select a.sd_ssm_stime as stime,a.sd_ssm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}	

		$db->query($query);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( 	"stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"filter" => " ",
						);
			
			array_push($result, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}

	
	

	public static function getStationData_sd_ivl( $table, $component,$ids ) {
		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$unit = "";
		$filter = "";
		$query="";
		if($component == 'Swarm Distance'){
			$unit = "km";
			$attribute = "sd_ivl_hdist";
			$query = "select a.sd_ivl_eqtype  as filter ,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Mean Depth'){
			$unit = "km";
			$attribute = "sd_ivl_avgdepth";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Vertical Dispersion'){
			$unit = "km";
			$attribute = "sd_ivl_vdispers";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Horiz-Migration'){
			$unit = "km";
			$attribute = "sd_ivl_hmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Vert-Migration'){
			$unit = "km";
			$attribute = "sd_ivl_vmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Counts'){
			$unit = "counts";
			$attribute = "sd_ivl_nrec";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Total Seismic Energy'){
			$unit = "counts";
			$attribute = "sd_ivl_nfelt";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Felt Earthquake Counts'){
			$unit = "Erg";
			$attribute = "sd_ivl_etot";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min-frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-frequency'){
			$unit = "Hz";
			$attribute = "sd_ivl_fmax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min-amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-amplitude'){
			$unit = "cm";
			$attribute = "sd_ivl_amax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}

		$db->query($query, $id);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( 	"stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"filter" => $row["filter"]
						);
			
			
			array_push($data, $temp );

		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;
	}

	public static function getStationData_sd_trm( $table, $component,$ids ) {

		global $db;
		$id = $ids["sta_id"];
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		$unit = "";
		$query="";
		if($component == 'Tremor Dominant Frequency-1'){
			$unit = "Hz";
			$attribute = "sd_trm_domfreq1";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Dominant Frequency-2'){
			$unit = "Hz";
			$attribute = "sd_trm_domfreq2";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Max-Amplitude'){
			$unit = "cm";
			$attribute = "sd_trm_maxamp";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Reduced Displacement'){
			$unit = "cm2";
			$attribute = "sd_trm_reddis";
			$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}

		$db->query($query);
		$res = $db->getList();
		foreach ($res as $row) {
			
			$stime = strtotime($row["stime"]);
			$etime = strtotime($row["etime"]);
			$temp = array( 	"stime" => floatval(1000 * $stime) ,
							"etime" => floatval(1000 * $etime) ,
							"value" => floatval($row["value"]),
							"filter" => $row["filter"],
						);
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		return $result;		
		
	}

}

SeismicRepository::$infor = json_decode( file_get_contents("Seismic.json", true) , true);