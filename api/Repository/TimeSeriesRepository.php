<?php
/**
 *	This class supports query the data series (deformation, gas, seismic..) for a volcano
 * 	
 */
//DEFINE('HOST', 'localhost');
DEFINE('HOST', 'www.wovodat.org');

class TimeSeriesRepository {

	private static function saveSerie($newSerie) {
	$series = json_decode(file_get_contents('Serie/Series.json', true), true);
	$found = false;
	foreach ($series as $key => $serie) {
		if ($serie['sr_id'] == $newSerie['sr_id']) {
			$series[$key] = $newSerie;
			$found = true;
		}
	}
	if (!$found)
		$series[] = $newSerie;
		file_put_contents('Serie/Series.json', json_encode($series), FILE_USE_INCLUDE_PATH);
	}

	private static function getSerieInfo($sr_id) {
		$series = json_decode(file_get_contents('Serie/Series.json', true), true);
		foreach ($series as $serie){
			if ($serie['sr_id'] == $sr_id){
				return $serie;
			}
		}
		return null;
	}

	private static function preprocessSerieData($data) {
		$results = array();
		foreach ($data as $a) {
			$b = array();
			if (array_key_exists('etime', $a)) {
				$b['start_time'] = $a['0'];
				$b['end_time'] = $a['etime'];
			} else {
				$b['time'] = $a['0'];
			}
			$b['value'] = $a['1'];

			$results[] = $b;
		}

	return $results;
	}

	/**
	*	Given a volcano Id, return all stations data belonged to it
	*	Stations are categorized by category
	*	@param: 
	*		$vd_id
	*	@return"
	*		data list
	*/
	public static function getTimeSeriesList($vd_id) {
		global $db;
		$query = "SELECT vd_cavw FROM vd WHERE vd_id = %d";

		$db->query($query, $vd_id);
		$vd_cavw = $db->getValue();

		$strs = explode(';', file_get_contents("http://" . HOST . "/php/switch.php?get=TimeSeriesForVolcano&cavw=" . $vd_cavw));

		$series = array();

		foreach ($strs as $str) {
			$splitted = explode('&', $str);

			if (count($splitted) < 5){
				continue;
			}

			$serie['sr_id'] = md5($str);
			$serie['category'] = $splitted[0];
		
		
		
			$serie['data_type'] = $splitted[1];
			$serie['station_code'] = $splitted[2];
			if (array_key_exists(5, $splitted)){
				$serie['component'] = $splitted[5];
			}else{
				$serie['component'] = '';
			}
			
			self::saveSerie($serie);	
			
			// if(array_key_exists($serie['category'],$series)){
			// 	$series[$serie['category']][] = $serie;
			// 	//echo "\t";
			// } else{
			// 	$series[$serie['category']] = $serie;
			// 	//echo "\n";
			// }
			//echo $serie['category'];
			$series[] = $serie;
		}

	return $series;
	}

	public static function getTimeSerie($sr_id) {
		$serie = self::getSerieInfo($sr_id);
		if (!$serie)
			return null;

		// Construct the url.
		$url = 'http://' . HOST . '/php/switch.php?get=StationData';
		$url .= '&type=' . strtolower($serie['category']);
		$url .= '&table=' . $serie['data_type'];
		$url .= '&code=' . $serie['station_code'];
		$url .= '&component=' . $serie['component'];

		// Fetch the url.
		//    $content = self::preprocessSerieData(json_decode(file_get_contents($url), true)[0]);
		$content = self::preprocessSerieData(json_decode(file_get_contents($url), true));
		$serie['data'] = $content;

	return $serie;
	}
} 