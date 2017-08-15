<?php
use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Sessions;
use Apps\Models\Profiledp;
use Apps\Models\ProfileRating;
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
					->leftjoin('profiledp','profile.id','=','profiledp.profile_id')
					->select('profile.id','profile.uniq_id','profile.firstname','profile.lastname','profile.phone','profile.address',
						'profiledp.dp','profile.web','profile.skills','profile.about','profile.role_id',
						'profile.created_at','profile.updated_at')
					->where('profile.status','1')
					->first();

		$count = ProfileRating::select('rating')
				->where('pro_id',$profile['id'])
				->count();

		if (($count) && ($profile)) {

			$sum =  ProfileRating::select('rating')
				->where('pro_id',$profile['id'])
				->sum('rating');

			$rating = (($sum/$count));

			$profile->rating = $rating;
			$profile->count = $count;

    	}
    	else {

    		if ($profile){

    			$profile->rating = 0;
				$profile->count = 0;
			}
   		}


		return $m->data($response,$profile);
		

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

    		$dp = Profiledp::where('profile_id',$profileCheck['id'])
    				->first();
    		$dp->dp = $request->getParam('dp');
    		$dp->save();

    		unset($Values['org_id'],$Values['user_id'],$Values['role_id']);
    		$Values['firstname'] = $Values['org_name'];
    		unset($Values['org_name'],$Values['uniq_id'],$Values['dp']);

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

    		$dp = Profiledp::where('profile_id',$profileCheck['id'])
    				->first();
    		$dp->dp = $request->getParam('dp');
    		$dp->save();

    		unset($Values['org_id'],$Values['user_id'],$Values['role_id'],$Values['dp']);


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


			$uniqid = strtoupper(uniqid("CL"));

			$profileDb = Profile::create([

   				'firstname' => $request->getParam('org_name'),
   				'phone' => $request->getParam('phone'),
   				'address' => $request->getParam('address'),
   				'web' => $request->getParam('web'),
   				'about' => $request->getParam('about'),
   				'org_id' => $user['org_id'],
   				'user_id' => $user['id'],
   				'role_id' => $user['role_id'],
   				'status' => '1',
   				'uniq_id' => $uniqid,
   			]);

   			$dp = Profiledp::create([

   				'dp' => $request->getParam('dp'),
   				'profile_id' => $profileDb['id'],

   				]);

			if ($profileDb) {

				$msg = array( 'profile_id' => $profileDb['id']);

				return $m->data($response,$msg);
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
    		$uniqid = strtoupper(uniqid("CL"));

			$profileDb = Profile::create([

   				'firstname' => $request->getParam('firstname'),
   				'lastname' => $request->getParam('lastname'),
   				'phone' => $request->getParam('phone'),
   				'address' => $request->getParam('address'),
   				'web' => $request->getParam('web'),
   				'about' => $request->getParam('about'),
   				'skills' => $request->getParam('skills'),
   				'org_id' => $user['org_id'],
   				'user_id' => $user['id'],
   				'role_id' => $user['role_id'],
   				'status' => '1',
   				'uniq_id' => $uniqid,

   			]);

   			$dp = Profiledp::create([

   				'dp' => $request->getParam('dp'),
   				'profile_id' => $profileDb['id'],

   			]);
		
			if ($profileDb) {

				$msg = array( 'profile_id' => $profileDb['id']);

				return $m->data($response,$msg);
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
