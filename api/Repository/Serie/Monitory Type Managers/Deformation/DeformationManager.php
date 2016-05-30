<?php 
class DeformationManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "dd_";
		$result[$prefix."tlt"] = new dd_tltManager;
		// $result[$prefix."tlv"] = new dd_tlvManager;
		$result[$prefix."str"] = new dd_strManager;
		$result[$prefix."edm"] = new dd_edmManager;
		$result[$prefix."ang"] = new dd_angManager;
		$result[$prefix."gps"] = new dd_gpsManager;
		$result[$prefix."gpv"] = new dd_gpvManager;
		$result[$prefix."lev"] = new dd_levManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Deformation';
	}
	
}

