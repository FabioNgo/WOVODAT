<?php
	/**
	*	This class supports query the information from vd table
	* 	
	*/
	class VolcanoRepository {

		/**
		*
		*
		*/
		public static function getVolcanoList() {
			$result = array();
			global $db;
			
			$sql = "select vd_id, vd_name, vd_cavw from vd ORDER BY vd_name";
			$db->query($sql);
			return $db->getList();
		}
	}
