<?php
	class FilterColorController {

		/**
		*	@return 
		*		volcano list
		*/
		public static function loadFilterColor() {
			$result = FilterColorRepository::getFilterColorList();
			
			return $result;
		}	

	}
