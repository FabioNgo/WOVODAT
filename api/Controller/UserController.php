<?php
	class UserController {
		public static function addUser($id,$name,$email,$institution){
//		    echo("asd");
            $query = "INSERT INTO ddu (cr_id,ddu_name,ddu_email,ddu_obs,ddu_ip,ddu_time,ddu_country,ddu_city,vd_name,cc_id,ddu_dataType,ddu_dataStartTime,ddu_dataEndTime) VALUES ('','$name','$email','$institution','',$id,'','','','','','','')";
            global $db;
            var_dump($db->query($query));
        }
	}