<?php
/**
 * Created by PhpStorm.
 * User: Luis Ngo
 * Date: 12/10/2016
 * Time: 10:38 PM
 */
class UserController {
    public static function addUser($name,$email,$institution,$vdName,$dataType){
        session_start();
        include 'php/include/db_connect.php';
        global $link;
        if(strcmp($name,"") || strcmp($email,"") || strcmp($institution,"")) {
            $_SESSION['downloadDataUsername'] = $name;
            $_SESSION['downloadDataUseremail'] = $email;
            $_SESSION['downloadDataUserobs'] = $institution;
        }
        $ipaddress= $_SERVER['REMOTE_ADDR'];
        $dateTime= date('Y-m-d h:i:s');
        $json = file_get_contents("http://ipinfo.io/$ipaddress");
        $details = json_decode($json);
        $sql = "select distinct cc_id from vd where vd_name='$vdName'";  // Get data owner id
        $result = mysql_query($sql, $link);
        $row = mysql_fetch_array($result);
        var_dump($row);
        if(isset($_SESSION['downloadDataUsername'])){
            $sql = "INSERT INTO ddu (ddu_name,ddu_email,ddu_obs,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTIme) values ('{$_SESSION['downloadDataUsername']}',' {$_SESSION['downloadDataUseremail']  }','{$_SESSION['downloadDataUserobs']}','$ipaddress','$dateTime','$details->country','$details->city','$vdName','{$row['cc_id']}',' DATATYPE [eg.Network Events ( VT )]  ',' Download Data Start time ', ' Download Data End Time' )";
            $result = mysql_query($sql, $link);
        }
        else if (isset($_SESSION['login'])) {
            $ccId = $_SESSION['login']['cc_id'];
            $sql = "select distinct cr_id from cr where cc_id= $ccId ";      // Get registered user id
            $result = mysql_query($sql, $link);
            $row1 = mysql_fetch_array($result);
            $sql="insert into ddu (cr_id,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTIme) values ({$row1['cr_id']},'$ipaddress','$dateTime','$details->country','$details->city','$vdName','{$row ['cc_id']}',',' DATATYPE [eg.Network Events ( VT )]  ',' Download Data Start time ', ' Download Data End Time' )";
            $result = mysql_query($sql, $link);
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
        if(isset($_SESSION['downloadDataUsername'])){
            $body .= "The unregistered user called '". $_SESSION['downloadDataUsername'] ."' from this ".$_SESSION['downloadDataUserobs']." Inst/Obs downloaded '".$dataType."' data for '".$vdName."' volcano today.\n\n";
        }else if(isset($_SESSION['login']['cr_uname'])){
            $body .= "The registered user called '". $_SESSION['login']['cr_uname'] ."' downloaded  '".$dataType."' data for '".$vdName."' volcano today.\n\n";
        }
        $body .= "Thanks,\n". "The WOVOdat team";
// Send email
        $mail->send($to, $headers, $body);
    }
}