<?php
/**
 *	T
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
abstract class GasTablesManager extends TableManager {
	protected function setStationTable(){
		return array("gs","cs");
	}
	
	protected function setMonitoryType(){
		return "Gas";
	} // monitory type Deformation, Gas, ....
	
} 