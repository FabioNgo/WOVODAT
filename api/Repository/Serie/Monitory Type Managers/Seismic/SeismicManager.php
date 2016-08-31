<?php 
class SeismicManager extends MonitoryTypeManager{

	protected function setTableManagers(){
		$result = array();
		
		$prefix = "sd_";
		$result[$prefix."evn"] = new sd_evnManager;
		$result[$prefix."evs"] = new sd_evsManager;
		$result[$prefix."int"] = new sd_intManager;
		$result[$prefix."trm"] = new sd_trmManager;
		$result[$prefix."ivl"] = new sd_ivlManager;
		$result[$prefix."rsm"] = new sd_rsmManager;
		$result[$prefix."ssm"] = new sd_ssmManager;
		$result[$prefix."evn_loc_eq_counts"] = new sd_evn_loc_eq_countsManager;
		return $result;
	}
	protected function setMonitoryType(){
		return 'Seismic';
	}
	
}

