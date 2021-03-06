<?php
namespace Apps\Controllers;

use Apps\Models\Sessions;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Firebase\JWT\JWTAuth;
use Tuupola\Base62;
use Apps\Controllers\Messages as m;
#use Acme\StoreBundle\Repository\DateTime;

class Token{

	function create($user,$now,$future){

    	$jti = (new Base62)->encode(random_bytes(16));
    	$payload = [
        	"iat" => $now->getTimeStamp(),
        	"exp" => $future->getTimeStamp(),
        	"jti" => $jti,
        	"sub" => $user,
   		];
   		$secret = base64_encode('4uv6XCa04uv6XCa0');
    	$token = JWT::encode($payload, $secret, "HS256");
    	$data["token"] = $token;
    	$data["expires"] = $future->getTimeStamp();
    	return $data;
    }

   	function validate($authHeader){

   		
   		$string = implode("",$authHeader);
  
   		list($jwt) = sscanf( $string, 'Bearer %s');

   		if($jwt){

        $session = Sessions::select('token','user_id')
                    ->where('token',$jwt)
                    ->where('status',1)
                    ->first();
        
        if ($session)
        {

          try{

            $secret = base64_encode('4uv6XCa04uv6XCa0');
            $token = JWT::decode($jwt, $secret, array('HS256'));
            return $session->user_id;

          }
          catch (\Firebase\JWT\ExpiredException $e){

            header('HTTP/1.0 401 Unauthorized');
            header('Content-Type: application/json;charset=utf-8');
            $msg = $e->getMessage();
            $message['response_status']  = array(
              'status' => 'failed',
              'message' => $msg,
            );
            $message['response_data'] = array();
            print json_encode($message);
            die();
          }
        }
        else {

             header('HTTP/1.0 400 Bad Request');
        }
      }
   }

   	function invalidate($authHeader){

   		$string = implode("",$authHeader);
  
   		list($jwt) = sscanf( $string, 'Bearer %s');

      $session = Sessions::where('token',$jwt)->where('status',1)->first();

      if ($session){

        $false = 0;
        $session->status = $false;
        $session->save();
        return true;
      }
   } 

   function examtoken($user,$now,$future){

      $jti = (new Base62)->encode(random_bytes(16));
      $payload = [
          "iat" => $now->getTimeStamp(),
          "exp" => $future->getTimeStamp(),
          "jti" => $jti,
          "sub" => $user,
      ];
      $secret = base64_encode('exam4uv6XCa04uv6XCa0');
      $token = JWT::encode($payload, $secret, "HS256");
      $data["token"] = $token;
      $data["expires"] = $future->getTimeStamp();
      return $data;
    }

    function validateExam($authHeader){

      
      $string = implode("",$authHeader);
  
      list($jwt) = sscanf( $string, 'Bearer %s');

      if($jwt){


          try{

            $secret = base64_encode('exam4uv6XCa04uv6XCa0');
            $token = JWT::decode($jwt, $secret, array('HS256'));
           
            return True;

          }
          catch (\Firebase\JWT\ExpiredException $e){

            header('HTTP/1.0 401 Unauthorized');
            header('Content-Type: application/json;charset=utf-8');
            $msg = $e->getMessage();
            $message['response_status']  = array(
              'status' => 'failed',
              'message' => $msg,
            );
            $message['response_data'] = array();
            print json_encode($message);
            die();
          }
      }
   }
}