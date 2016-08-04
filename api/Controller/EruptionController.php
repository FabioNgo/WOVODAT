<?php
	class EruptionController {
		/**
		*	Return eruption list belonging to a specific volcano
		*	@param: 
		*		volcano_id
		*	@return:
		*		eruption list
		*/
		public static function loadEruptionForecastList($vd_id,$offline) {
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			$data = EruptionRepository::getEruptionForecastList($vd_id);
			if($offline){
				file_put_contents('../offline-data/eruption_forecast.json', json_encode($data));
			}
			return $data;
		}

		/**
		*	Return eruption list belonging to a specific volcano
		*	@param: 
		*		volcano_id
		*	@return:
		*		eruption list
		*/
		public static function loadEruptionList($vd_id,$offline) {
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			$data =  EruptionRepository::getEruptionList($vd_id);
			if($offline){
				file_put_contents('../offline-data/eruption_list.json', json_encode($data));
			}
			return $data;
		}
	}