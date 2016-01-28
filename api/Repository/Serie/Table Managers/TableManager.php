<?php
/**
 *	This class supports query the data from data table 
 * 	
 */
// DEFINE('HOST', 'localhost');
require_once('TableManagerInterface.php');
abstract class TableManager implements TableManagerInterface {
	protected $cols_name;
	protected $table_name;
	protected $monitoryType;
	protected $dataType;
	protected $stationId;
	protected $stationCode;
	public function TableManager(){
		$this->cols_name = $this->setColumnsName();
		$this->table_name = $this->setTableName();
		$this->monitoryType = $this->setMonitoryType();
		$this->dataType = $this->setDataType();
		$this->stationId = $this->setStationID();
		$this->stationCode = $this->setStationCode();
	}
	abstract protected function setColumnsName(); // names of data columns
	abstract protected function setTableName(); // name of es table
	abstract protected function setMonitoryType(); // monitory type Deformation, Gas, ....
	abstract protected function setDataType(); // Data type for each data table
	//if there is 1 station, station1 is the same as station2
	abstract protected function setStationID(); // column names represent stationID1,station ID2
	abstract protected function setStationCode(); // column name represent primary stationCode1, stationCode2.
	abstract protected function setStationDataParams($component); // params to get data station [unit,flot_style,errorbar,attributes,query]
	public function getTimeSeriesList($vd_id,$stations){
  		$result = array();
		global $db;
		$query_format = 'select a.%s as sta_id1,  a.%s as sta_code1,a.%s as sta_id2,a.%s as sta_code2 ';
		$query = sprintf($query_format,$this->stationId[0],$this->stationCode[0],$this->stationId[1],$this->stationCode[1]);
		foreach ($this->cols_name as $name) {
			$query = $query.",a.".$name;
		}
		$query = $query." from $this->table_name as a where a.vd_id=$vd_id";
		// echo($query);
		$db->query( $query);

		// echo($this->monitoryType."   ".$query."\n");
		$serie_list = $db->getList();
		// var_dump($serie_list);
		foreach ($serie_list as $serie) {

			foreach ($this->cols_name as $col_name) {

				if($serie[$col_name]!=""){

					$x = array('category' => $this->monitoryType ,
							   'data_type' => $this->dataType,
							   'station_id1' => $serie["sta_id1"],
							   'station_code1' => $serie["sta_code1"],
							   'station_id2' => $serie["sta_id2"],
							   'station_code2' => $serie["sta_code2"],
						       'component' => $serie[$col_name],
						   		);

					$x["sr_id"] = md5( $x["category"].$x["data_type"].$x["station_id1"].$x["station_id2"].$x["component"] );
					array_push($result,  $x );

				}
			}
		}		
		// var_dump($result);
		return $result;

 	}

  	public function getStationData($stations){
  		
  		$id1 = $stations["station_id1"];
  		$id2 = $stations["station_id2"];
		global $db;
		// $cc = ', a.cc_id, a.cc_id2, a.cc_id3 ';
		$result = array();
		$res = array();
		$data = array();
		$unit = "";
		$stationDataParams = $this->setStationDataParams($stations['component']);
		$errorbar = $stationDataParams["errorbar"];
		$query = $stationDataParams["query"];
		$db->query($query, $id1,$id2);
		// echo($query);
		// var_dump($this);
		// // var_dump($this);
		$res = $db->getList();
		foreach ($res as $row) {
			// var_dump($row);
			//add value attributes
			$temp = array("value" => floatval($row["value"]));
			//add time value attributes (time or (etime, stime))
			if(array_key_exists("time", $row)){
				$time = strtotime($row["time"]);
				$temp["time"] = floatval(1000*$time);
			}else{
				$stime = strtotime($row["stime"]);
				$etime = 0;
				if(array_key_exists("etime", $row)){
					$etime = strtotime($row["etime"]);
				}else{
					// if data have no etime, assume that the bars are continuous
					$data_size = sizeof($data);
					if($data_size!=0){
						$data[$data_size-1]["etime"] = $stime*1000;
					}
				}
				
				$temp["stime"] = floatval(1000*$stime);
				$temp["etime"] = floatval(1000*$etime);
			}
			//add filter attribute
			// var_dump($row);
			if(array_key_exists("filter", $row)){
				
				$temp["filter"] = $row["filter"];
				if($temp["filter"] == null){
					$temp["filter"] = "NULL";
				}
			}else{
				$temp["filter"] = " ";
			}
			// find attribute
			if(array_key_exists("unit", $row)){
				
				$unit = $row["unit"];
				
			}else{
				$unit = $stationDataParams["unit"];
			}
			// add error bar
			if($errorbar){
				if($row["err"]!=null){
					$temp["error"] = floatval($row["err"]);
				}else{
					$temp["error"] = 0;
				}
			}
			array_push($data, $temp );			
		}
		$result["style"] = $stationDataParams["style"];
		$result["errorbar"] = $errorbar;
		$result["data"] = $data;
		$result["unit"] = $unit;
		// var_dump($result);
		return $result;
  	}
} 