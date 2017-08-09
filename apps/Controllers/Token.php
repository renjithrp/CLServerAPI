<?php
namespace Apps\Controllers;

use Apps\Models\Sessions;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Firebase\JWT\JWTAuth;
use Tuupola\Base62;
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

        $session = Sessions::select('token')
                    ->where('token',$jwt)
                    ->where('status',1)
                    ->first();
        
        if ($session)
        {

          try{

            $secret = base64_encode('4uv6XCa04uv6XCa0');
            $token = JWT::decode($jwt, $secret, array('HS256'));
            return True;

          }
          catch (Exception $e){

            header('HTTP/1.0 401 Unauthorized');
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
   	
}