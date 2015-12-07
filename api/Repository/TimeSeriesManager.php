<?php
/**
 *	This class supports query the data series (deformation, gas, seismic..) for a volcano
 * 	
 */
DEFINE('HOST', 'localhost');

class TimeSeriesManager {
  private static $instance;
  protected $timeSeriesManagers;
  public static function getInstance(){
    if(self::$instance==null){
      self::$instance = new TimeSeriesManager;

    }
    return self::$instance;
  }

  private function TimeSeriesManager(){
    $this->timeSeriesManagers = array("Deformation" => new DeformationManager,
                                      "Seismic" => new SeismicManager,
                                      "Gas" => new GasManager,
                                      "Hydrology" => new HydrologyManager,
                                      "Thermal" =>new ThermalManager,
                                      "Meteo" =>new MeteoManager,
                                      "Fields" => new FieldsManager);
    
  }

  private function saveSerie($series) {
    file_put_contents('Serie/Series.json', json_encode($series), FILE_USE_INCLUDE_PATH);
  }

  private function getSerieInfo($sr_id) {
    $series = json_decode(file_get_contents('Serie/Series.json', true), true);
    foreach ($series as $serie)
      if ($serie['sr_id'] == $sr_id)
        return $serie;
    return null;
  }

	public function getTimeSeriesList($vd_id) {
		$result = array();
    // var_dump($this->timeSeriesManagers);
    foreach ($this->timeSeriesManagers as $type => $class){
      $series = $class->getTimeSeriesList($vd_id);
      $result = array_merge($result,$series);
    }
    self::saveSerie($result);
		return $result;
	}	


  public function getTimeSerie($sr_id) {

    $serie = $this->getSerieInfo($sr_id);
    if (!$serie)
      return null;
    $serie['data'] = array();
    foreach ($this->timeSeriesManagers as $type => $class){
      if($type == $serie['category']){
        $data = $class->getStationData($serie);
        $serie['data'] = array_merge($serie['data'],$data);
      }
    }
    return $serie;
  }


} 