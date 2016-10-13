<?php

//    require "PHPMailer/PHPMailer.php";
class UserController
{
    public static function addUser($id, $name, $email, $institution)
    {
//		    echo("asd");
        $query = "INSERT INTO ddu (cr_id,ddu_name,ddu_email,ddu_obs,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTime) VALUES ('','$name','$email','$institution','',$id,'','','','','','','')";
        global $db;
//            var_dump($db->query($query));
        require_once "Mail-1.3.0/Mail.php";

// New mail object


//$email = $row[1];   //Data owner email
        $host = "smtp.gmail.com";
        $username = "ngolebaotrung@gmail.com";
        $password = "baotrung";
        $mail = Mail::factory('smtp', array('host' => $host, 'auth' => true, 'username' => $username, 'password' => $password));
//        if (PEAR::isError($mail)) {
//            echo $mail->getMessage() . "\n" . $mail->getUserInfo() . "\n";
//            die();
//        }
        $email = "CWidiwijayanti@ntu.edu.sg";   //Data owner email
        $user_name = "Data owner";
        $to = $user_name . " <" . "ngolebaotrung@gmail.com" . ">";

        $from = "noreply@wovodat.org";
//        $cc = "CC:  CWidiwijayanti@ntu.edu.sg , nangthinzarwin1@gmail.com";
        $subject = "Summary of downloaded data list using WOVOdat visualization tools<NOT SPAM>";
        $headers = array("From" => $from, "Subject" => $subject);

        $body = "Hi, \n\n";

        if (isset($_SESSION['downloadDataUsername'])) {
            $body .= "The unregistered user called '" . $_SESSION['downloadDataUsername'] . "' from this " . $_SESSION['downloadDataUserobs'] . " Inst/Obs downloaded '" . $dataType . "' data for '" . $data[1][0] . "' volcano today.\n\n";

        } else if (isset($_SESSION['login']['cr_uname'])) {

            $body .= "The registered user called '" . $_SESSION['login']['cr_uname'] . "' downloaded  '" . $dataType . "' data for '" . $data[1][0] . "' volcano today.\n\n";
        }

        $body .= "Thanks,\n" . "The WOVOdat team";

// Send email
//        echo("asd");
        $mail->send($to, $headers, $body);
//        echo("ASd");

    }
}