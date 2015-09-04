<?php
/**
 *	This class supports query the data series (deformation, gas, seismic..) for a volcano
 * 	
 */
DEFINE('HOST', 'localhost');

class TimeSeriesRepository {

  private static function saveSerie($newSerie) {
    $series = json_decode(file_get_contents('Serie/Series.json', true), true);
    // var_dump($series);
    $found = false;
    if($series != null){
      foreach ($series as $key => $serie) {
        if ($serie['sr_id'] == $newSerie['sr_id']) {
          $series[$key] = $newSerie;
          $found = true;
        }
      }
    }
    if (!$found)
      $series[] = $newSerie;
    file_put_contents('Serie/Series.json', json_encode($series), FILE_USE_INCLUDE_PATH);
  }

  private static function getSerieInfo($sr_id) {
    // var_dump($sr_id);
    $series = json_decode(file_get_contents('Serie/Series.json', true), true);
    // var_dump($series);
    foreach ($series as $serie)
      if ($serie['sr_id'] == $sr_id)
        return $serie;
    return null;
  }

  

	public static function getTimeSeriesList($vd_id) {
		$result = array();
		
		$DATA_LIST = array( "Seismic", "Deformation", "Gas",  "Meteo" , "Hydrology");
		// var_dump($DATA_LIST);
		foreach ($DATA_LIST as $value) {
			//$series = call_user_func_array($value.'Repository::getTimeSeriesList', [$vd_id]);
		
			$series = call_user_func_array($value."Repository::getTimeSeriesList", array($vd_id));
			
			foreach ($series as $serie) {
				self::saveSerie($serie);
				array_push($result, $serie);
			}
		}
		return $result;
	}	


  public static function getTimeSerie($sr_id) {

    $serie = self::getSerieInfo($sr_id);
    // var_dump($serie);
    if (!$serie)
      return null;
    $serie['data'] = call_user_func_array( $serie['category']."Repository::getStationData" , 
      array( $serie['data_type'] , 
        isset( $serie['station_code']) ? $serie['station_code'] : $serie['volcanoID'],
        $serie['component'], $serie["id"] )  );
    // var_dump($serie['data']);
    // var_dump($serie);
    return $serie;
    // return null;
  }

  // public static function getTimeSerie($sr_id) {
  //   $serie = self::getSerieInfo($sr_id);
  //   if (!$serie)
  //     return null;

  //   // Construct the url.
  //   $url = 'http://' . HOST . '/php/switch.php?get=StationData';
  //   $url .= '&type=' . strtolower($serie['category']);
  //   $url .= '&table=' . $serie['data_type'];
  //   $url .= '&code=' . $serie['station_code'];
  //   $url .= '&component=' . $serie['component'];

  //   // Fetch the url.
  //   $fetch = json_decode(file_get_contents($url), true);

  //   $content = self::preprocessSerieData($fetch[0]);

  //   $serie['data'] = $content;
  //   return $serie;
  // }


  // public static function getTimeSeriesList($vd_id) {
  //  global $db;
  //  $query = "SELECT vd_cavw FROM vd WHERE vd_id = %d";
  //  $db->query($query, $vd_id);
  //  $vd_cavw = $db->getValue();

  //   $strs = explode(';', file_get_contents("http://" . HOST . "/php/switch.php?get=TimeSeriesForVolcano&cavw=" . $vd_cavw));

  //   $series = array();
    
  //  foreach ($strs as $str) {
  //    $splitted = explode('&', $str);

  //    if (count($splitted) < 5)
  //      continue;

  //    $serie['sr_id'] = md5($str);
  //    $serie['category'] = $splitted[0];
  //    $serie['data_type'] = $splitted[1];
  //    $serie['station_code'] = $splitted[2];
  //     if (array_key_exists(5, $splitted))
  //       $serie['component'] = $splitted[5];
  //     else
  //       $serie['component'] = '';
      
  //     self::saveSerie($serie); 
      
  //     $series[] = $serie;
  //  }

  //   return $series;
  // }
} 