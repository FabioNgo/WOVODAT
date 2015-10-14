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
		$result = array_merge($result,self::getTimeSeriesList_sd_int($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_rsm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_ssm($vd_id,$stations));
		$result = array_merge($result,self::getTimeSeriesList_sd_trm($vd_id,$stations));

		return $result;
	}

	private static function getTimeSeriesList_sd_evs( $vd_id, $stations ) {
		$result = array();
		global $db;
				
		$query="
			select distinct a.sta_id,a.sta_code as ss_code,concat('S-P Arrival Time') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_spint IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Epicenter From Event') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_dist_actven IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Max-amplitude') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_maxamptrac IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Dominant Frequency') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_domFre IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Magnitude') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_mag IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Energy') as type from jjcn_sta as a, sd_evs as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_evs_energy IS NOT NULL 
			";

		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicEventFromNetwork",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					   

					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
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
					   'sta_id' => $serie["sta_id"],
					   
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_trm( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ss_code,concat('Tremor Dominant Frequency-1') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_trm_domfreq1 IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Tremor Dominant Frequency-2') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_trm_domfreq2 IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Tremor Max-Amplitude') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_trm_maxamp IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('Reduced Displacement') as type from jjcn_sta as a, sd_trm as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_trm_reddis IS NOT NULL	
			";
			
		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicTremor",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_ivl( $vd_id, $stations ) {
		$result = array();
		global $db;
		$query="
			select distinct a.sta_id,a.sta_code as ss_code,concat('Swarm Distance') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_hdist IS NOT NULL 
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Swarm Mean Depth') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_avgdepth IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Swarm Vertical Dispersion') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_vdispers IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Hypocenter Horiz-Migration') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_hmigr_hyp IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Hypocenter Vert-Migration') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_vmigr_hyp IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Counts') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_nrec IS NOT NULL 
			union			
			select distinct a.sta_id,a.sta_code as ss_code,concat('Total Seismic Energy') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_nfelt IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Felt Earthquake Counts') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_etot IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Min Frequency') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_fmin IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Max Frequency') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_fmax IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Min Amplitude') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_amin IS NOT NULL
			union
			select distinct a.sta_id,a.sta_code as ss_code,concat('Earthquake Max Amplitude') as type from jjcn_sta as a, sd_ivl as b where a.type='Seismic' and a.vd_id=$vd_id and a.sta_id=b.ss_id and b.sd_ivl_amax IS NOT NULL
			";

		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list) ; $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicInterval",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					  
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}
		return $result;
	}

	private static function getTimeSeriesList_sd_rsm( $vd_id, $stations ) {
		$result = array();
		global $db;

		$query="select distinct a.sta_id,a.sta_code as ss_code,concat('RSAM Counts') as type from jjcn_sta as a, sd_rsm as b, sd_sam as c where a.type='Seismic' and a.vd_id=% and a.sta_id=c.ss_id and b.sd_sam_id=c.sd_sam_id and b.sd_rsm_count IS NOT NULL";

		$db->query( $query, $vd_id);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "RSAM",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_ssm( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="
			select distinct a.sta_id,a.sta_code as ss_code,concat('SSAM Low-freq Limit') as type from jjcn_sta as a, sd_ssm as b, sd_sam as c where a.type='Seismic' and a.vd_id=% and a.sta_id=c.ss_id and b.sd_sam_id=c.sd_sam_id and b.sd_ssm_lowf IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('SSAM Hight-freq Limit') as type from jjcn_sta as a, sd_ssm as b,sd_sam as c where a.type='Seismic' and a.vd_id=% and a.sta_id=c.ss_id and b.sd_sam_id=c.sd_sam_id and b.sd_ssm_highf IS NOT NULL 
			union 
			select distinct a.sta_id,a.sta_code as ss_code,concat('SSAM Counts') as type from jjcn_sta as a, sd_ssm as b, sd_sam as c where a.type='Seismic' and a.vd_id=% and a.sta_id=c.ss_id and b.sd_sam_id=c.sd_sam_id and b.sd_ssm_count IS NOT NULL 
			";

		$db->query( $query);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SSAM",
					   'station_code' => $serie["ss_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
				
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	private static function getTimeSeriesList_sd_evn( $vd_id, $stations ) {
		$result = array();
		global $db;
				

		$query="select distinct c.sn_id as sta_id,c.sn_code as sn_code,concat('Earthquake Depth') as type from jjcn_sta as a, sd_evn as b, sn as c where a.type='Seismic' and a.vd_id=$vd_id and b.sn_id=c.sn_id and b.sd_evn_edep IS NOT NULL  
		union
		select distinct c.sn_id as sta_id,c.sn_code as sn_code,concat('Earthquake Magnitude') as type from jjcn_sta as a, sd_evn as b, sn as c where a.type='Seismic' and a.vd_id=$vd_id and b.sn_id=c.sn_id and b.sd_evn_pmag IS NOT NULL";

		
		$db->query( $query, $vd_id,$vd_id);
		
		$serie_list = $db->getList();

		for ($i=0; $i<sizeof($serie_list); $i++) { 
			$serie = $serie_list[$i];
				$x = array('category' => "Seismic" ,
					   'data_type' => "SeismicEventFromNetwork",
					   'station_code' => $serie["sn_code"],
					   'component' => $serie["type"],
					   'sta_id' => $serie["sta_id"],
					 
					   );
			$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_code"].$x["component"] );
 			array_push($result,  $x );
		}	
		return $result;
	}

	public static function getStationData( $table, $code, $component,$id ) {
		foreach (self::$infor as $key => $type) {
			if ( $type["data_type"] == $table ) {
				return call_user_func_array("self::getStationData_".$key, array( $key, $component,$id) );
				
			}
		}
	} 
	private static function getStationData_sd_evn( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'Earthquake Depth'){
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

		$db->query($query);

		$res = $db->getList();
		
		foreach ($res as $row) {
			
			$time = strtotime($row["time"]);
			$temp = array( 	"time" => floatval(1000 * $time) ,
							"value" => floatval($row["value"]),
							"filter" => $row["filter"],
						);
			if($errorbar){
				$temp["error"] = $row["err"];
			}
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}
	public static function getStationData_sd_evs( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'S-P Arrival Time'){

				$attribute = "sd_evs_spint";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";

		}else if($component == 'Epicenter From Event'){
				$attribute = "sd_evs_dist_actven";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max-Amplitude'){
				$attribute = "sd_evs_maxamptrac";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Dominant Frequency'){
				$attribute = "sd_evs_domFre";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Magnitude'){
				$attribute = "sd_evs_mag";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Energy'){
				$attribute = "sd_evs_energy";
				$query = "select a.sd_evs_eqtype  as filter ,a.sd_evs_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}

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
		if($component == 'Max Distance Felt'){
				$attribute = "sd_int_maxdist";
				$query = "select a.sd_int_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Max-intensity'){
				$attribute = "sd_int_maxrint";
				$query = "select a.sd_int_time as time, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Distance at Max-intensity'){
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
		return $result;
	}

	public static function getStationData_sd_rsm( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'RSAM Counts'){
				$attribute = "sd_rsm_count";
				$query = "select a.sd_rsm_stime as stime,a.sd_rsm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
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
			
			array_push($data, $temp );
		}
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		return $result;
	}

	public static function getStationData_sd_ssm( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'SSAM Low-freq Limit'){
				$attribute = "sd_ssm_lowf";
				$query = "select a.sd_ssm_stime as stime,a.sd_ssm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'SSAM Hight-freq Limit'){
				$attribute = "sd_ssm_highf";
				$query = "select a.sd_ssm_stime as stime,a.sd_ssm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'SSAM Counts'){
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
		return $result;
	}

	
	

	public static function getStationData_sd_ivl( $table, $component,$id ) {
		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'Swarm Distance'){
				$attribute = "sd_ivl_hdist";
				$query = "select a.sd_ivl_eqtype  as filter ,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Mean Depth'){
			$attribute = "sd_ivl_avgdepth";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value  $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Swarm Vertical Dispersion'){
			$attribute = "sd_ivl_vdispers";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Horiz-Migration'){
			$attribute = "sd_ivl_hmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Hypocenter Vert-Migration'){
			$attribute = "sd_ivl_vmigr_hyp";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Counts'){
			$attribute = "sd_ivl_nrec";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Total Seismic Energy'){
			$attribute = "sd_ivl_nfelt";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Felt Earthquake Counts'){
			$attribute = "sd_ivl_etot";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min Frequency'){
			$attribute = "sd_ivl_fmin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max Frequency'){
			$attribute = "sd_ivl_fmax";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Min Amplitude'){
			$attribute = "sd_ivl_amin";
			$query = "select a.sd_ivl_eqtype  as filter,a.sd_ivl_stime as stime,a.sd_ivl_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.$attribute IS NOT NULL";
		}else if($component == 'Earthquake Max Amplitude'){
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
		return $result;
	}

	public static function getStationData_sd_trm( $table, $component,$id ) {

		global $db;
		$cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$attribute = "";
		$style = "bar";
		$errorbar = false;
		$data = array();
		$filter = "";
		if($component == 'Tremor Dominant Frequency-1'){
				$attribute = "sd_trm_domfreq1";
				$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Dominant Frequency-2'){
				$attribute = "sd_trm_domfreq2";
				$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Tremor Max-Amplitude'){
				$attribute = "sd_trm_maxamp";
				$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}else if($component == 'Reduced Displacement'){
				$attribute = "sd_trm_reddis";
				$query = "select a.sd_trm_type  as filter,a.sd_trm_stime as stime,a.sd_trm_etime as etime, a.$attribute as value $cc from $table  as a where a.ss_id=$id and a.attribute IS NOT NULL";
		}

		$db->query($query);
		$result["style"] = $style;
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
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
		return $result;			
		
	}

}

SeismicRepository::$infor = json_decode( file_get_contents("Seismic.json", true) , true);