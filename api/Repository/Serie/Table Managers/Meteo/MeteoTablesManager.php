<?php
/**
 *	T
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
abstract class MeteoTablesManager extends TableManager {
	protected function setStationTable(){
		return "ms";
	}
	
	protected function setMonitoryType(){
		return "Meteology";
	} // monitory type Deformation, Gas, ....
	
} 