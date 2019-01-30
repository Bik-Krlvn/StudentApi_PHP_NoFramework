<?php
include_once '../../libs/php-jwt/src/BeforeValidException.php';
include_once '../../libs/php-jwt/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt/src/JWT.php';
include_once '../../libs/php-jwt/src/ExpiredException.php';
use \Firebase\JWT\JWT;

class Token  
{
   public static function DecodeToken($token,$key,$alg)
   {
       # code...
       $decode = null;
       if($token){
          try {
              //code...
              $decode = JWT::decode($token,$key,$alg);
              return $decode;
          } catch (Exception $e) {
              //throw $th;
              http_response_code(401);
              echo json_encode(array('message'=>'Token Decoding Failed','errors'=>$e->getMessage()));
          }
       }
       return $decode;
   }

   public static function EncodeToken($jwt,$data)
   {
       # code...
       $encode = null;
       if($jwt && $data){
           extract($jwt);
           extract($data);
           $token = array(
               "iss" => $iss,
               "aud" => $aud,
               "iat" => $iat,
               "nbf" => $nbf,
               "data" => array(
                   "id"       => $id,
                   "firstname"=> $firstname,
                   "lastname" => $lastname,
                   "email"    => $email
               )
           );
           $encode = JWT::encode($token,$key);
           return $encode;
       }
       return $encode;
   }

   public static function Authenticate($token,$key,$alg)
   {
       # code...
       if($token){
          try {
              //code...
              $decode = JWT::decode($token,$key,$alg);
              if($decode) return true;
          } catch (Exception $e) {
              //throw $th;
             printf($e->getMessage());
          }
       }
       return false;
   }
}
