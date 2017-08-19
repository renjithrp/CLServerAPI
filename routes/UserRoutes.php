<?php

use Apps\Models\Users;
use Apps\Models\Verification;
use Apps\Models\Sessions;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;
use Apps\Controllers\Messages as m;
use Apps\Controllers\SendEmail;


function UserLogin ($request, $response, $args) {

  $server = $request->getServerParams();
  $now = new DateTime();
	$future = new DateTime("now +5 hours");
  $m = new m;

  if ((isset($server["PHP_AUTH_USER"])) || (isset($server['PHP_AUTH_PW'])) ){

    $email = $server["PHP_AUTH_USER"];
    $password = $server['PHP_AUTH_PW'];

    $user = Users::where('email',$email)->first();
      
    if (!$user) {

      return $m->failed($response,'Invalid User or password');

    }

    $token = new Apps\Controllers\Token;
    $data = $token->create($server,$now,$future);
    $status = True;
    if (password_verify($password, $user->password)){

        $id = new Getid;
        $gname = new GetName;
        $orgId = $id->org($user->id);
        $profileID = $id->profile($user->id)[0];

        $name = $gname->name($user->id);

    		$userinfo = array('profile_id' => $profileID ,
        'firstname' => $name['firstname'],
        'lastname' => $name['lastname'],
    		'email' => $user->email,
    		'role_id' => $user->role_id,
    		'org_id' => $user->org_id,
    		'secure' => $data,
    		);

        $session = Sessions::create([
   			  'token' => $data['token'],
   			  'user_id' => $user->id,
   			  'created_at' => $now,
   			  'valid_till' => $future,
   			  'status' => $status,
   		   ]);

        if ($session){
        
          return $m->data($response,$userinfo);
        }
        else{

          $m->error($response);
        }
    }
    else {

      return $m->failed($response,'Invalid User or password');
    }
  }
  else {

    return $m->failed($response,'Invalid User or password');
  }
}

 function UserLogout ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
  $m = new m;

  if ($jwt) {

		$invalidate = $token->invalidate($security);
		if ($invalidate){

      return $m->success($response,'User has been logedout');
		}
		else {

      return $m->failed($response,'Invalid token');
		}
	}
	else {

    return $m->failed($response,'Invalid token');
	} 
}

function UserSignup($request, $response, $args) {

	/*
	** Important message for developers **
	Role 101 - Organization
	Role 102 - Staff
	Role 103 - Student
	Role 104 - Parent

	Newely created user will be disable initially by setting status flag to '0'
	*/
	#Development purpose only
	$status = True; # set this to False when it goes to production

	$validation = new Apps\Validation\Validator;
  $m = new m;
	$validation->validate($request, [
		  'email' => v::noWhitespace()->notEmpty()->emailAvailable(),
		  'password' => v::noWhitespace()->notEmpty(),
		  'role_id'	=> v::noWhitespace()->notEmpty(),
	   ]);

	if ($validation->failed()){
	
		$errors = array('status' => 'failed',
			'message' => $_SESSION['errors'],

			);
		unset($_SESSION['errors']);

    $message = array();
    $message['response_status'] = $errors;
    $message['response_data'] = array();
    return $response->withStatus(200)
          ->withHeader("Content-Type", "application/json")
          ->withJson($message);
    
	}
   	$role_id =  $request->getParam('role_id');

   	if ($role_id == 101){

   		$org_id = 0;
   	}
   	else {

   		$org_id = $request->getParam('org_id');
      $id = new Getid;
      $checkid = $id->org($org_id)->first();


      if (!$checkid) {

         return $m->error($response);

      }
   	}

   	Users::create([
   		'email' => $request->getParam('email'),
   		'name' => $request->getParam('name'),
   		'password' => password_hash($request->getParam('password'),PASSWORD_DEFAULT),
   		'org_id' => $org_id,
   		'role_id' => $role_id,
   		'status' => $status,
   		]);

   	$user = Users::where('email',$request->getParam('email'))->first();

   	if ($user->org_id == 0) {

   		$user->org_id = $user->id;
   		$user->save();
   	}

    $email = $request->getParam('email');
    $mail = new SendEmail;

    $i = 0; //counter
      $pin = ""; //our default pin is blank.
      while($i < 4){
        //generate a random number between 0 and 9.
          $pin .= mt_rand(1, 9);
          $i++;
      }

    Verification::create([
        'email' => $email,
        'code' => $pin,
        'status' => 1,
      ]);

    $status = $mail->verification($email,$pin);
    return $m->success($response, "$email:password");
    if ($status){
      return $m->success($response, "Verification code send to $email");
    }
    else {
      return $m->error($response);
    }
}
