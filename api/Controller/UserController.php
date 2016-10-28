<?php
/**
 * Created by PhpStorm.
 * User: Luis Ngo
 * Date: 12/10/2016
 * Time: 10:38 PM
 */



class UserController {
    public static function addUser($name,$email,$institution,$vdName,$dataType,$startTime,$endTime){
        global $db;
        session_start();
        include 'php/include/db_connect.php';
//        var_dump($dataType);
//        var_dump($startTime);
//        var_dump($endTime);
//        for ( $i = 0 ; $i < 3; $i++){
//            echo "$dataType[$i] ','$startTime[$i] ', ' $endTime[$i]\n";
//        }


        if(strcmp($name,"") != 0 && strcmp($email,"") != 0 && strcmp($institution,"") != 0) {
            $_SESSION['downloadDataUsername'] = $name;
            $_SESSION['downloadDataUseremail'] = $email;
            $_SESSION['downloadDataUserobs'] = $institution;
        }
        // var_dump($name .' '. $email . ' ' . $institution );
//        $ipaddress= $_SERVER['REMOTE_ADDR'];

        $dateTime= date('Y-m-d h:i:s');

        $jsonIp = file_get_contents("https://api.ipify.org?format=json");
        $temp = json_decode($jsonIp);
        $ipaddress = $temp->ip;
        $json = file_get_contents("http://ipinfo.io/$ipaddress");

        $details = json_decode($json);


        $sql = "select distinct cc_id from vd where vd_name='$vdName'";  // Get data owner id
        $db->query($sql);
        $result = $db->getList();
        $row = $result[0];
        for($i = 0 ; $i < count($dataType); $i++){

            $sTime = date('Y-m-d h:i:s',$startTime[$i]/1000);
            $eTime = date('Y-m-d h:i:s',$endTime[$i]/1000);
//            var_dump($startTime[$i]);
//            var_dump($endTime[$i]);
            var_dump($sTime);
            var_dump($eTime);
            if (isset($_SESSION['login'])) {

                $ccId = $_SESSION['login']['cc_id'];
                $sql = "select distinct cr_id from cr where cc_id= $ccId ";      // Get registered user id
                $db->query($sql);
                $result = $db->getList();
                $row1 = $result[0];
                $sql="insert into ddu (cr_id,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTime) values ({$row1['cr_id']},'$ipaddress','$dateTime','$details->country','$details->city','$vdName','{$row ['cc_id']}',' $dataType[$i] ','$sTime ', ' $eTime ' )";

                $db->query($sql);
//                $db->getList();

            }else if(isset($_SESSION['downloadDataUsername'])){

                $sql = "INSERT INTO ddu (ddu_name,ddu_email,ddu_obs,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTime) values ('{$_SESSION['downloadDataUsername']}',' {$_SESSION['downloadDataUseremail']  }','{$_SESSION['downloadDataUserobs']}','$ipaddress','$dateTime','$details->country','$details->city','$vdName','{$row['cc_id']}','$dataType[$i] ',' $sTime ', '$eTime' )";
                $db->query($sql);
//                $db->getList();

            }
        }





        /**
         * SEND EMAIL
         */
        /***********
        Email content example:

        Hi,

        The unregistered user called 'John' from this EOS Inst/Obs downloaded 'sdEvn' data for 'Miyake-jima' volcano today.

        (OR)

        The registered user called 'Nang' downloaded  'Tilt' data for 'Mayon' volcano today.


        Thanks,
        The WOVOdat team

         ***********/
        // Include PEAR Mail package
        require_once "Mail-1.2.0/Mail.php";

        $mail=Mail::factory("mail");


        $email = "CWidiwijayanti@ntu.edu.sg";   //Data owner email
        $user_name = "Data owner";
        $to=$user_name." <".$email.">";

        $from="noreply@wovodat.org";
        $cc = "CC:  CWidiwijayanti@ntu.edu.sg , nangthinzarwin1@gmail.com";
        $subject="Summary of downloaded data list using WOVOdat visualization tools<NOT SPAM>";
        $headers=array("From"=>$from,"CC"=>$cc,"Subject"=>$subject);

        $body="Hi, \n\n";
        $dataTypeString = join(",",$dataType);
        if(isset($_SESSION['downloadDataUsername'])){
            $body .= "The unregistered user called '". $_SESSION['downloadDataUsername'] ."' from this ".$_SESSION['downloadDataUserobs']." Inst/Obs downloaded '".$dataTypeString."' data for '".$vdName."' volcano today.\n\n";

        }else if(isset($_SESSION['login']['cr_uname'])){

            $body .= "The registered user called '". $_SESSION['login']['cr_uname'] ."' downloaded  '".$dataTypeString."' data for '".$vdName."' volcano today.\n\n";
        }

        $body .= "Thanks,\n". "The WOVOdat team";

// Send email
        $mail->send($to, $headers, $body);
        return false;

    }
}