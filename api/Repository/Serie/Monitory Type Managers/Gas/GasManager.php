<?php 
class GasManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "gd_";
		$result["gd"] = new gdManager;
		$result[$prefix."flu"] = new gd_pluManager;
		$result[$prefix."sol"] = new gd_solManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Gas';
	}
	
}

