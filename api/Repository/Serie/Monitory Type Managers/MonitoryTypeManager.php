<?php 
require_once('MonitoryTypeManagerInterface.php');
abstract class MonitoryTypeManager implements MonitoryTypeManagerInterface {
	protected $infor;
	protected $tableManagers;
	protected $timeSeriesListQuery = "";
	protected $type;
	public function MonitoryTypeManager(){

		$this->tableManagers = $this->setTableManagers();
		$this->type = $this->setMonitoryType();
		$this->timeSeriesListQuery = "select vd_id,sta_code as ds_code from jjcn_sta as a where a.vd_id = %s AND a.type=$this->type ";
		$this->infor = json_decode( file_get_contents($this->type."/".$this->type.".json", true) , true);
	}
	abstract protected function setMonitoryType();
	abstract protected function setTableManagers();
	public function getTimeSeriesList($vd_id){
		$result = array();
		global $db;
		$db->query($this->timeSeriesListQuery,$vd_id);
		$stations = $db->getList();

		foreach ($this->tableManagers as $col => $tableManager) {
			// var_dump($tableManager);	
			$result = array_merge($result,$tableManager->getTimeSeriesList($vd_id,$stations));
			// var_dump($result);
		}
		// var_dump($result);
		return $result;
	}
	public function getStationData($serie ){
		$result = array();
		// var_dump($serie);
		$table = "";
		foreach ($this->infor as $key => $type) if ( $type["data_type"] == $serie["data_type"] ){
			$table = $key;
		}
		foreach ($this->tableManagers as $tableName=> $tableManager) {
			if($table == $tableName){
				$result = array_merge($result,$tableManager->getStationData($serie));
			}
			
		}

		return $result;
	}
	
}


