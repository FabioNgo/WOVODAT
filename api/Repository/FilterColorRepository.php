<?php
	/**
	*	This class supports query the information from vd table
	* 	
	*/
	class FilterColorRepository {

		/**
		*
		*
		*/
		public static function getFilterColorList() {
			$result = array();
			global $db;
			
			$sql = "select a.st_eqt_wovo as type,a.st_sqt_color as color from st_eqt as a";
			$db->query($sql);
			return $db->getList();
		}
	}
