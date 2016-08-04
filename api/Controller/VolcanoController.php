<?php
	class VolcanoController {

		/**
		*	@return 
		*		volcano list
		*/
		public static function loadVolcanoList($offline) {
			$result = VolcanoRepository::getVolcanoList();
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			if($offline){
				file_put_contents('../offline-data/volcano_list.json', json_encode($result));
			}
			return $result;
		}	

	}
