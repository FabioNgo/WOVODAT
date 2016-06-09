<?php
/**
 *	T
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
abstract class SeismicTablesManager extends TableManager {
	protected function setStationTable(){
		return "ss";
	}
	
	protected function setMonitoryType(){
		return "Seismic";
	} // monitory type Deformation, Gas, ....
	
} 