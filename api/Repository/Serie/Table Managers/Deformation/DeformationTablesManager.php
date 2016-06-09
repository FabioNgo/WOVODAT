<?php
/**
 *	T
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
abstract class DeformationTablesManager extends TableManager {
	protected function setStationTable(){
		return "ds";
	}
	
	protected function setMonitoryType(){
		return "Deformation";
	} // monitory type Deformation, Gas, ....
	
} 