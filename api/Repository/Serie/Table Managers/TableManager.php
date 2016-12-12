<?php
/**
 *    This class supports query the data from data table
 *
 */
// DEFINE('HOST', 'localhost');
require_once('TableManagerInterface.php');

abstract class TableManager implements TableManagerInterface
{
    protected $cols_name;
    protected $table_name;
    protected $monitoryType;
    protected $dataType;
    protected $stationId;
    protected $stationCode;
    protected $sta_table;
    protected $sta_id_code_dictionary;
    protected $vd_long;
    protected $vd_lat;
    protected $data_code;
    private $cc_id_table = array();
    private $cb_ids_table = array();
    // The relationship between 2 ids
    private $idsRelationship = "and"; //
    public function TableManager()
    {
        $this->cols_name = $this->setColumnsName();
        $this->table_name = $this->setTableName();
        $this->monitoryType = $this->setMonitoryType();
        $this->dataType = $this->setDataType();
        $this->stationId = $this->setStationID();
        $this->sta_id_code_dictionary = $this->getStationIdCodeDictionary();
        $this->shortDataType = $this->setShortDataType();
        $this->idsRelationship = $this->setIdsRelationship();
        $this->data_code = $this->setDataCode();

    }
    //Return 'and' or 'or'
    protected function setIdsRelationship(){
        return "and";
    }
    //must return 1 sta_code column
    protected function getStationCodeQuery($sta_id)
    {
        $table_name = $this->getTableNameFromIdName($sta_id);
        $sta_id_code_query = "SELECT DISTINCT " . $table_name . "_id as sta_id, " . $table_name . "_code as sta_code FROM $table_name order by sta_id";
        return $sta_id_code_query;
    }

    protected function getStationIdCodeDictionary()
    {
        global $db;
        $sta_id_codes = array();
        $old_sta_id = "";
        foreach ($this->stationId as $sta_id) {
            if ($sta_id == $old_sta_id) {

            } else {

                $old_sta_id = $sta_id;
                $sta_id_code = array();
                $sta_id_code_query = $this->getStationCodeQuery($sta_id);
//                echo $sta_id_code_query . "\n";
                $db->query($sta_id_code_query);
                $temp = $db->getList();
                $sta_id_code[0] = "";
                // print_r($temp);
                foreach ($temp as $tmp) {
                    $sta_id_code[$tmp["sta_id"]] = $tmp['sta_code'];
                }
            }

            array_push($sta_id_codes, $sta_id_code);
        }

        return $sta_id_codes;
    }

    abstract protected function setStationTable(); // get the name of the station table which stores sta_id and sta_code

    abstract protected function setColumnsName(); // names of data columns

    abstract protected function setTableName(); // name of es table

    abstract protected function setMonitoryType(); // monitory type Deformation, Gas, ....

    abstract protected function setDataType(); // Data type for each data table

    abstract protected function setShortDataType();

    //if there is 1 station, station1 is the same as station2
    abstract protected function setStationID(); // column names represent stationID1,station ID2

    protected function setDataCode()
    {
        $code = substr($this->table_name, 3, strlen($this->table_name) - 3) . "_code";
//		var_dump($code);
        return $code;

    }

    abstract protected function setStationCode(); // column name represent primary stationCode1, stationCode2. (Deprecated)

    abstract protected function setStationDataParams($component); // params to get data station [unit,flot_style,errorbar,attributes,query]

    private function getTableNameFromIdName($id)
    {
        $temp = explode("_", $id);
        return $temp[0];
    }

    protected function getTimeSeriesListQuery($vd_id)
    {
        $query_format = 'select b.vd_inf_slat as vd_lat, b.vd_inf_slon as vd_long, a.%s as sta_id1,  a.%s as sta_id2, vd.vd_name ';
        $query = sprintf($query_format, $this->stationId[0], $this->stationId[1]);
        foreach ($this->cols_name as $name) {
            $query = $query . ",a." . $name;
        }
        $query = $query . " from $this->table_name as a,vd ,vd_inf as b where a.vd_id=$vd_id AND vd.vd_id = $vd_id and b.vd_id = $vd_id group by a.vd_id, sta_id1, sta_id2 order by a.vd_id";
        return $query;
    }

    public function getTimeSeriesList($vd_id)
    {

        $result = array();
        global $db;
        $query = $this->getTimeSeriesListQuery($vd_id);
//        $query = $db->query($query);
//        echo $query."\n";
//        return [];
        $serie_list = $db->getList();
        $exsited = array();
        $v = "";

        foreach ($serie_list as $serie) {

//            var_dump($serie);
//            var_dump($this->sta_id_code_dictionary);
            foreach ($this->cols_name as $col_name) {
                /*
                 * if station id is not valid, set it to be zero
                 */
                if (!array_key_exists($serie["sta_id1"], $this->sta_id_code_dictionary[0])) {
                    $serie["sta_id1"] = "0";
                }
                if (!array_key_exists($serie["sta_id2"], $this->sta_id_code_dictionary[1])) {
                    $serie["sta_id2"] = "0";
                }
                if ($serie["sta_id1"] == "0" && $serie["sta_id2"] == "0") {
                    continue;
                }
                /*
                 * In "Or" relationship, the first id has more priority
                 */
                if($this->idsRelationship == "or"){
                    if($serie["sta_id1"]!="0"){
                        $serie["sta_id2"] = "0";
                    }
                }
                if (array_key_exists("vd_name", $serie)) {
                    $v = $serie["vd_name"];
                } else {

                }

                if ($serie[$col_name] != "") {

                    $x = array('category' => $this->monitoryType,
                        'data_type' => $this->dataType,
                        'short_data_type' => $this->shortDataType,
                        'station_id1' => $serie["sta_id1"],
                        'station_code1' => $this->sta_id_code_dictionary[0][$serie["sta_id1"]],
                        'station_id2' => $serie["sta_id2"],
                        'station_code2' => $this->sta_id_code_dictionary[1][$serie["sta_id2"]],
                        'component' => trim($serie[$col_name]),
                        'vd_lat' => $serie["vd_lat"],
                        'vd_long' => $serie["vd_long"],
                        'volcanoName' => $v,
//								'data_owner' => $cc_url,
                    );

                    $x["sr_id"] = md5($x["category"] . $x["data_type"] . $x["station_id1"] . $x["station_id2"] . $x["component"] . $x["volcanoName"]);
                    if (!array_key_exists($x["sr_id"], $exsited)) {
                        $exsited[$x["sr_id"]] = true;
                        array_push($result, $x);
                    } else {

                    }


                }
            }
        }
        return $result;

    }

    public function getStationData($stations)
    {

        $this->vd_long = $stations["vd_long"];
        $this->vd_lat = $stations["vd_lat"];
        $id1 = $stations["station_id1"];
        $id2 = $stations["station_id2"];

        global $db;
        $result = array();
        $data = array();
        $unit = "";
        $stationDataParams = $this->setStationDataParams($stations['component']);
        $errorbar = $stationDataParams["errorbar"];
        $query = $stationDataParams["query"];
        //Add select data code from query. Add in this tableManager to apply all data.
        $temp = "select cc_id, cc_id2, cc_id3, cb_ids,";

        $query = str_replace("select", $temp, $query);

        $query = $db->query($query, $id1, $id2);
//        echo $query."\n";
//        return [];
        $res = $db->getList();

        foreach ($res as $row) {
            //add value attributes

            $temp = array("value" => floatval(number_format($row["value"], 2, '.', ',')));

            //add time value attributes (time or (etime, stime))
            if (array_key_exists("time", $row)) {
                $time = strtotime($row["time"]);
                $temp["time"] = floatval(1000 * $time);
            } else {
                $stime = strtotime($row["stime"]);
                $etime = 0;
                if (array_key_exists("etime", $row)) {
                    $etime = strtotime($row["etime"]);
                } else {
                    // if data have no etime, assume that the bars are continuous
                    $data_size = sizeof($data);
                    if ($data_size != 0) {
                        $data[$data_size - 1]["etime"] = $stime * 1000;
                    }
                }

                $temp["stime"] = floatval(1000 * $stime);
                $temp["etime"] = floatval(1000 * $etime);
            }
            //add filter attribute
            // var_dump($row);

            if (array_key_exists("filter", $row)) {

                $temp["filter"] = $row["filter"];
                if ($temp["filter"] === null) {
                    // echo("a\n");
                    $temp["filter"] = " ";
                }
                if ($temp["filter"] == "") {
                    $temp["filter"] = "Others";
                }
            } else {
                $temp["filter"] = " ";
            }
            // find attribute
            if (array_key_exists("unit", $row)) {

                $unit = $row["unit"];

            } else {
                $unit = $stationDataParams["unit"];
            }
            // add error bar
            if ($errorbar) {
                if ($row["err"] != null) {
                    $temp["error"] = floatval($row["err"]);
                } else {
                    $temp["error"] = 0;
                }
            }
//            $temp["data_code"] = $row["data_code"];
            $cc_ids = array();
            $cc_ids[0] = $row["cc_id"];
            $cc_ids[1] = $row["cc_id2"];
            $cc_ids[2] = $row["cc_id3"];
            $cb_ids = $row["cb_ids"];
//            echo $cc_ids[2];
            $dataOwner = $this->getCCUrl($cc_ids);
            $temp["data_owner"] = $dataOwner;

            $reference = $this->getDataReference($cb_ids);
            $temp["reference"] = $reference;
            array_push($data, $temp);
        }
        $result["style"] = $stationDataParams["style"];
        $result["errorbar"] = $errorbar;
        $result["data"] = $data;
        $result["unit"] = $unit;
        // var_dump($result);
        return $result;
    }

    /*
     * Get cc_url/cc_email of a volcano
     * of a specific cavw
     */

    private function getCCUrl($cc_ids)
    {
        $dataOwners = array();
        global $db;
        foreach ($cc_ids as $cc_id) {

            if ($cc_id != null) {
                if (array_key_exists($cc_id, $this->cc_id_table)) {
                    $result = $this->cc_id_table[$cc_id];

                } else {
                    $sql = "SELECT cc_code,cc_url, cc_email FROM cc WHERE cc_id=" . $cc_id;

                    $db->query($sql);
                    $result = $db->getList();
                    if (count($result) != 0) {
                        $result = $result[0];
                    }
                    $this->cc_id_table[$cc_id] = $result;
                }

                if ($result["cc_url"] != null) {
                    array_push($dataOwners, $result["cc_code"]);
                    array_push($dataOwners, $result["cc_url"]);
                } else if ($result["cc_url"] != null) {
                    array_push($dataOwners, $result["cc_code"]);
                    array_push($dataOwners, $result["cc_email"]);
                }
            }
        }
        return array_unique($dataOwners);
    }

    /**
     * Luis Ngo : 6/9/2016
     *
     * Get reference of data (cb)
     */
    private function getDataReference($cb_ids)
    {
        global $db;
        $reference = array();
        if ($cb_ids != null) {
            if (array_key_exists($cb_ids, $this->cb_ids_table)) {
                $result = $this->cb_ids_table[$cb_ids];

            } else {
                $sql = "SELECT cb_auth,cb_url FROM cb WHERE cb_id IN (" . $cb_ids . ")";
                $query = $db->query($sql);
//            echo $query."\n";
                $result = $db->getList();

                $this->cb_ids_table[$cb_ids] = $result;
            }
            foreach ($result as $row) {
                array_push($reference, $row["cb_auth"]);
                array_push($reference, $row["cb_url"]);
            }


        } else {
            array_push($reference, "");
            array_push($reference, "");
        }
        return $reference;
    }
} 