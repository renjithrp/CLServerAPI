<?php
use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Sessions;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;


$app->get('/profile', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		//get Organization 
		$org = Users::select('org_id')->where('id',$jwt)->first();

		$Organization = Profile::select('firstname as organization')
						->where('role_id','101')
						->where('id',$org['org_id'])->pluck('organization');

		$profile = Profile::where('profile.org_id',$org['org_id'])
					->where('profile.user_id',$jwt)
					->where('profile.status','1')
					->get();

		return $response->withJson($profile);
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

 });



$app->put('/profile', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);

	
	if ($jwt){

		$user = Users::select('id','role_id','org_id')
					->where('id',$jwt)
					->where('status','1')
					->first();

		if ($user['id'] == $user['org_id']){

			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'organization' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
						'message' => $_SESSION['errors'],
						);

				unset($_SESSION['errors']);
				return $response->withJson($errors);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if (!$profileCheck) {

        		$message = array(

   					'status' => 'error',
   					'message' => 'Profile not found',
   				);

				return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);

    		}


    		ProfileUpdate::where('id',$profileCheck)
    				->limit(1)
    				->update($request->getParam())
    		;
		

			$message = array(
   				'status' => 'success',
   				'message' => 'Profile created',
   			);
   			return $response->withJson($message,200);

		}
		else {

			
			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'firstname' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
						'message' => $_SESSION['errors'],
						);

				unset($_SESSION['errors']);
				return $response->withJson($errors);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if (!$profileCheck) {

        		$message = array(

   					'status' => 'error',
   					'message' => 'Profile not found',
   				);

				return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);

    		}

    		$Values = $request->getParsedBody();
    		unset($Values['org_id'],$Values['user_id'],$Values['role_id']);


    		$res = Profile::select('firstname','lastname')->where('id',$profileCheck['id'])
    				->limit(1)
    				->update($Values);

    		if ($res) {

    			$message = array(
   				'status' => 'success',
   				'message' => 'Profile updated',);
				return $response->withJson($message,200);
    		}
    		else {

    			$message = array(
   				'status' => 'failed',
   				'message' => 'Bad request',);
				return $response->withJson($message,500);
    		}
			


		}


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

});


$app->post('/profile', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);

	
	if ($jwt){

		$user = Users::select('id','role_id','org_id')
					->where('id',$jwt)
					->where('status','1')
					->first();

		if ($user['id'] == $user['org_id']){

			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'organization' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
						'message' => $_SESSION['errors'],
						);

				unset($_SESSION['errors']);
				return $response->withJson($errors);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if ($profileCheck) {

        		$message = array(

   					'status' => 'error',
   					'message' => 'Profile already exists',
   				);

				return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);

    		}

			Profile::create([

   				'firstname' => $request->getParam('organization'),
   				'phone' => $request->getParam('phone'),
   				'address' => $request->getParam('address'),
   				'web' => $request->getParam('web'),
   				'about' => $request->getParam('about'),
   				'dp' => $request->getParam('dp'),
   				'org_id' => $user['org_id'],
   				'user_id' => $user['id'],
   				'role_id' => $user['role_id'],
   				'status' => '1',
   			]);
		

			$message = array(
   				'status' => 'success',
   				'message' => 'Profile created',
   			);
   			return $response->withJson($message,200);

		}
		else {

			
			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'firstname' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
						'message' => $_SESSION['errors'],
						);

				unset($_SESSION['errors']);
				return $response->withJson($errors);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if ($profileCheck) {

        		$message = array(

   					'status' => 'error',
   					'message' => 'Profile already exists',
   				);

				return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);

    		}

			 Profile::create([

   				'firstname' => $request->getParam('firstname'),
   				'lastname' => $request->getParam('lastname'),
   				'phone' => $request->getParam('phone'),
   				'address' => $request->getParam('address'),
   				'web' => $request->getParam('web'),
   				'about' => $request->getParam('about'),
   				'skills' => $request->getParam('skills'),
   				'dp' => $request->getParam('dp'),
   				'org_id' => $user['org_id'],
   				'user_id' => $user['id'],
   				'role_id' => $user['role_id'],
   				'status' => '1',
   			]);
		

			$message = array(
   				'status' => 'success',
   				'message' => 'Profile created',
   			);
   			return $response->withJson($message,200);


		}


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


});
