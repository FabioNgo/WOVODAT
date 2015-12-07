<?php 
class FieldsManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "fd_";
		$result[$prefix."ele"] = new fd_eleManager;
		$result[$prefix."gra"] = new fd_graManager;
		$result[$prefix."mag"] = new fd_magManager;
		$result[$prefix."mgv"] = new fd_mgvManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Fields';
	}
	
}

