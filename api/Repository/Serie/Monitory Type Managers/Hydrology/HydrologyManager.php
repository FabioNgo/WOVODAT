<?php 
class HydrologyManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "hd_";
		$result["hd"] = new hdManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Hydrology';
	}
	
}

