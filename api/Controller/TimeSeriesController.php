<?php
	class TimeSeriesController {

		/**
		*	@param: 
		*		$vd_id
		*	@return 
		*		data list
		*/
		public static function loadDataList($vd_id) {
			$instance = TimeSeriesManager::getInstance();
			$result = $instance->getTimeSeriesList($vd_id);
			return $result;
		}	


		public static function loadTimeSerie($serie,$offline) {
			$instance = TimeSeriesManager::getInstance();
			$result = $instance->getTimeSerie($serie);
			if($offline){
				file_put_contents('../offline-data/'.$serie['sr_id'].'.json', json_encode($result));
			}
			return $result;
		}

	}
