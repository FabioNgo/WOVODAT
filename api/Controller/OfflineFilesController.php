<?php
	class OfflineFilesController {

		/**
		*	@return 
		*		volcano list
		*/
		public static function loadOfflineFiles() {
			$result = OfflineFilesRepository::getOfflineFilesList();
			return $result;
		}	

	}
