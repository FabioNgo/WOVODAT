<?php
/**
 *	T
 * 	
 */
// DEFINE('HOST', 'localhost');
// require_once '..//TableManager.php';
abstract class FieldsTablesManager extends TableManager {
	protected function setStationTable(){
		return "fs";
	}
	
	protected function setMonitoryType(){
		return "Fields";
	} // monitory type Deformation, Gas, ....
	
} 