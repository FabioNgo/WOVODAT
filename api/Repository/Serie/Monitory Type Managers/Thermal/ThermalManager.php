<?php 
class ThermalManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "td_";
		$result["td"] = new tdManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Thermal';
	}
	
}

