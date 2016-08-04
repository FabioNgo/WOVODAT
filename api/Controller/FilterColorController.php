<?php
	class FilterColorController {

		/**
		*	@return 
		*		volcano list
		*/
		public static function loadFilterColor($offline) {
			$result = FilterColorRepository::getFilterColorList();
			$offline = filter_var($offline, FILTER_VALIDATE_BOOLEAN);
			if($offline){
				file_put_contents('../offline-data/filter_color_list.json', json_encode($result));
			}
			return $result;
		}	

	}
