<?php
	class EruptionController {
		/**
		*	Return eruption list belonging to a specific volcano
		*	@param: 
		*		volcano_id
		*	@return:
		*		eruption list
		*/
		public static function loadEruptionForecastList($vd_id) {
			$data = EruptionRepository::getEruptionForecastList($vd_id);
			return $data;
		}

		/**
		*	Return eruption list belonging to a specific volcano
		*	@param: 
		*		volcano_id
		*	@return:
		*		eruption list
		*/
		public static function loadEruptionList($vd_id) {
			$data =  EruptionRepository::getEruptionList($vd_id);
			return $data;
		}
	}