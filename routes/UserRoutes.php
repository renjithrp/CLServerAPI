<?php
// Routes
use Apps\Models\Users;
use Apps\Models\Sessions;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;


$app->get('/login', function ($request, $response, $args) {


    $server = $request->getServerParams();
    $now = new DateTime();
	  $future = new DateTime("now +2 hours");

    $email = $server["PHP_AUTH_USER"];
    $password = $server['PHP_AUTH_PW'];

    $user = Users::where('email',$email)->first();
        if (!$user) {

    	return false;

    }

    $token = new Apps\Controllers\Token;
    $data = $token->create($server,$now,$future);
    $status = True;
    if (password_verify($password, $user->password)){

    		$_SESSION['user'] = array('id' => $user->id,
    		'email' => $user->email,
    		'role_id' => $user->role_id,
    		'org_id' => $user->org_id,
    		'secure' => $data,
    		);

      Sessions::create([
   			'token' => $data['token'],
   			'user_id' => $user->id,
   			'created_at' => $now,
   			'valid_till' => $future,
   			'status' => $status,
   		]); 
    
    	return $response->withStatus(201)
    		->withHeader("Content-Type", "application/json")
    		->withJson($_SESSION['user']);
    }


});


$app->get('/logout', function ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);

		$invalidate = $token->invalidate($security);
		if ($invalidate){

			$message = array(
   				'status' => 'success',
   				'message' => 'User has been logedout',
   			);
			return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
		}
		else {

			$message = array(
   				'status' => 'failed',
   				'message' => 'Invalid token',
   			);
			return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
		}
		
	/*}
	else {

		$message = array(
   		'status' => 'error',
   		'message' => 'Security token is not valid',
   		);
		return $response->withStatus(400)
    		->withHeader("Content-Type", "application/json")
    		->withJson($message);
	} */


});

$app->post('/signup', function ($request, $response, $args) {

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
	$validation->validate($request, [
		'email' => v::noWhitespace()->notEmpty()->emailAvailable(),
		'password' => v::noWhitespace()->notEmpty(),
		'role_id'	=> v::noWhitespace()->notEmpty(),

	]);
	if ($validation->failed()){
	
		$errors = array('status' => 'error',
			'message' => $_SESSION['errors'],

			);
		unset($_SESSION['errors']);
		return $response->withJson($errors);

	}


   	$role_id =  $request->getParam('role_id');

   	if ($role_id == 101){

   		$org_id = 0;
   	}
   	else {

   		$org_id = $request->getParam('org_id');
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
   	$message = array(
   		'status' => 'success',
   		'message' => 'Please check your email to complete signup process',
   		);
   	return $response->withJson($message,200);
});
