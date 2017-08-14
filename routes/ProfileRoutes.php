<?php
use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Sessions;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;


function GetProfile($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	$m = new m;
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

					//$a = strtoupper(uniqid("ASQ"));
					//echo $a;
					//exit;
		if ($profile) {

			return $m->data($response,$profile);
		}

	}
	else {
		return $m->failed($response,"Invalid token");
	}
 }

function UpdateProfile ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	$m = new m;

	if ($jwt){

		$user = Users::select('id','role_id','org_id')
					->where('id',$jwt)
					->where('status','1')
					->first();

		if ($user['id'] == $user['org_id']){

			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'org_name' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
						'message' => $_SESSION['errors'],
						);

				unset($_SESSION['errors']);
				$message = array();
    			$message['response_status'] = $errors;
    			$message['response_data'] = array();
    			return $response->withStatus(400)
          				->withHeader("Content-Type", "application/json")
          				->withJson($message);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if (!$profileCheck) {

        		return $m->failed($response,"Profile not found");
    		}

    		$Values = $request->getParsedBody();
    		unset($Values['org_id'],$Values['user_id'],$Values['role_id']);
    		$Values['firstname'] = $Values['org_name'];
    		unset($Values['org_name']);

    		$res = Profile::select('firstname','lastname')->where('id',$profileCheck['id'])
    				->limit(1)
    				->update($Values);
    	
			if ($res) {

				return $m->success($response,"Profile updated");
    		}
    		else {

    			return $m->error($response);
    		}
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
				$message = array();
    			$message['response_status'] = $errors;
    			$message['response_data'] = array();
    			return $response->withStatus(200)
          				->withHeader("Content-Type", "application/json")
          				->withJson($message);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if (!$profileCheck) {

        		return $m->failed($response,"Profile not found");
    		}

    		$Values = $request->getParsedBody();
    		unset($Values['org_id'],$Values['user_id'],$Values['role_id']);


    		$res = Profile::select('firstname','lastname')->where('id',$profileCheck['id'])
    				->limit(1)
    				->update($Values);

    		if ($res) {

				return $m->success($response,"Profile updated");
    		}
    		else {

    			return $m->error($response);
    		}
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}


function CreateProfile($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	$m = new m;

	if ($jwt){

		$user = Users::select('id','role_id','org_id')
					->where('id',$jwt)
					->where('status','1')
					->first();

		if ($user['id'] == $user['org_id']){

			$validation = new Apps\Validation\Validator;

			$validation->validate($request, [
				'org_name' => v::notEmpty(),
				'phone' => v::noWhitespace()->notEmpty(),
				'address'	=> v::notEmpty(),
				]);
			if ($validation->failed()){
	
				$errors = array('status' => 'error',
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

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if ($profileCheck) {

   				return $m->failed($response,"Profile already exists");
    		}

			$profileDb = Profile::create([

   				'firstname' => $request->getParam('org_name'),
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

			if ($profileDb) {

				return $m->success($response,"Profile created");
			}
			else{

				return $m->error($response);
			}
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
				$message = array();
    			$message['response_status'] = $errors;
    			$message['response_data'] = array();
    			return $response->withStatus(200)
          				->withHeader("Content-Type", "application/json")
          				->withJson($message);
			}

			$profileCheck = Profile::where('user_id',$user['id'])->first();
	
        	if ($profileCheck) {

				return $m->failed($response,"Profile already exists");
    		}
			$profileDb = Profile::create([

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
		
			if ($profileDb) {

				return $m->success($response,"Profile created");
			}
			else{

				return $m->error($response);
			}
		}
	}
	else {
		return $m->failed($response,"Invalid token");
	}
}
