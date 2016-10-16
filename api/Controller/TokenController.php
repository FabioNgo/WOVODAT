<?php
/**
 * Created by PhpStorm.
 * User: Luis Ngo
 * Date: 12/10/2016
 * Time: 10:38 PM
 */
class TokenController {
    public static function generateToken($name,$email){
        $key = 'bob-esponja';
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];
        $header = json_encode($header);
        $header = base64_encode($header);
        $payload = [
            'iss' => 'wovodat.com',
            'username' => $name,
            'email' => $email
        ];
        $payload = json_encode($payload);
        $payload = base64_encode($payload);
        $signature =  hash_hmac('sha256',"$header.$payload",$key,true);
        $signature = base64_encode($signature);
        $token = $header.$payload.$signature;
        $temp =  array('token' => $token);
        return $temp;
//            return json_encode($temp);
    }
    public static function checkToken($token){
//            echo $_SESSION['downloadDataUsername']."\n";
        if (isset($_SESSION['downloadDataUsername'])){
            $name = $_SESSION['downloadDataUsername'];
            $email= $_SESSION['downloadDataUseremail'];
            $t =  self::generateToken($name, $email)["token"];
            if (strcmp($t,$token)) return true;
        }
        return false;
    }
}