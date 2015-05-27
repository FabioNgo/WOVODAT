<?php
	class TimeSeriesController {

		/**
		*	@param: 
		*		$vd_id
		*	@return 
		*		data list
		*/
		public static function loadDataList($vd_id) {
			return TimeSeriesRepository::getTimeSeriesList($vd_id);
		}	


		public static function loadTimeSerie($sr_id) {
			return TimeSeriesRepository::getTimeSerie($sr_id);
		}

	}
