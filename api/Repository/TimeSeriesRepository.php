<?php
/**
 *	This class supports query the data series (deformation, gas, seismic..) for a volcano
 * 	
 */
DEFINE('HOST', 'localhost');

class TimeSeriesRepository {

  private static function saveSerie($series) {
     
    
    
    file_put_contents('Serie/Series.json', json_encode($series), FILE_USE_INCLUDE_PATH);
  }

  private static function getSerieInfo($sr_id) {
    $series = json_decode(file_get_contents('Serie/Series.json', true), true);
    foreach ($series as $serie)
      if ($serie['sr_id'] == $sr_id)
        return $serie;
    return null;
  }

  

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		
		// $DATA_LIST = array( "Seismic", "Deformation", "Gas",  "Meteo" , "Hydrology");
    $DATA_LIST = array("Deformation");
		foreach ($DATA_LIST as $value) {
			$series = call_user_func_array($value."Repository::getTimeSeriesList", array($vd_id));
			self::saveSerie($series);
			foreach ($series as $serie) {
				
				array_push($result, $serie);
			}
		}
		return $result;
	}	


  public static function getTimeSerie($sr_id) {

    $serie = self::getSerieInfo($sr_id);
    if (!$serie)
      return null;
    // var_dump($serie);
    $ids = array();
    if(array_key_exists("ds_id", $serie)){
      $ids['ds_id'] = $serie["ds_id"];
    }
    if(array_key_exists("ds_id1", $serie)){
      $ids['ds_id1'] = $serie["ds_id1"];
    }
    if(array_key_exists("ds_id2", $serie)){
      $ids['ds_id2'] = $serie["ds_id2"];
    }
    // var_dump($ids);
    $serie['data'] = call_user_func_array( $serie['category']."Repository::getStationData" , 
      array( $serie['data_type'] , 
        isset( $serie['station_code']) ? $serie['station_code'] : $serie['volcanoID'],
        $serie['component'], $ids )  );
    // $serie['data'] = 
    return $serie;
  }


} 