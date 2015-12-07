<?php 
class MeteoManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "med_";
		$result["med"] = new medManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Meteo';
	}
	
}

