<?php
	class TimeSeriesController {

		/**
		*	@param: 
		*		$vd_id
		*	@return 
		*		data list
		*/
		public static function loadDataList($vd_id,$offline) {
			$instance = TimeSeriesManager::getInstance();
			$result = $instance->getTimeSeriesList($vd_id);
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			// var_dump($offline);
			if($offline){
				
				for ($i =0 ; $i<count($result);$i++) {
					$result[$i]["offline"] = true;
				}
				// var_dump($result);
				file_put_contents('../offline-data/time_series_list.json', json_encode($result));
			}
			return $result;
		}	


		public static function loadTimeSerie($serie,$offline) {
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			$instance = TimeSeriesManager::getInstance();
			$result = $instance->getTimeSerie($serie);
			if($offline){
				file_put_contents('../offline-data/'.$serie['sr_id'].'.json', json_encode($result));
			}
			return $result;
		}

	}
