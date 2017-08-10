<?php

use Apps\Models\Profile;
use Apps\Models\Users;
use Apps\Models\Testimonials;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;

$app->get('/testimonials', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profile = Profile::select('id','role_id')
					->where('user_id',$jwt)
					->first();
		if ($profile){

			$Testimonials = Testimonials::rightjoin('profile','profile.user_id','=','testimonials.user_id')
					->rightjoin('role','profile.role_id','=','role.id')
					->select('testimonials.id','profile.id as profile_id',
						'profile.firstname','profile.lastname','role.name as role',
						'testimonials.comments',
						'testimonials.created_at','testimonials.updated_at')
					->where('testimonials.pro_id',$profile['id'])
					->where('testimonials.status',1)
					->orderBy('testimonials.created_at', 'DESC')
					->get();


			return $response->withJson($Testimonials);

		}		
    	else {

    		$message = array(
   				'status' => 'error',
   				'message' => 'Invalid data',
   			);
			return $response->withStatus(500)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
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

$app->get('/testimonials/{pro_id}', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profileID = $request->getAttribute('pro_id');

		$User = Users::select('org_id')->where('id',$jwt)->first();

		$Profile = Profile::select('org_id','user_id')
						->where('id',$profileID)->first();
		if ($User['org_id'] == $Profile['org_id']){

			$Testimonials = Testimonials::rightjoin('profile','profile.user_id','=','testimonials.user_id')
					->rightjoin('role','profile.role_id','=','role.id')
					->select('testimonials.id','profile.id as profile_id',
						'profile.firstname','profile.lastname','role.name as role',
						'testimonials.comments',
						'testimonials.created_at','testimonials.updated_at')
					->where('testimonials.pro_id',$profileID)
					->where('testimonials.status',1)
					->orderBy('testimonials.created_at', 'DESC')
					->get();
		
			return $response->withJson($Testimonials);
		}
		else {

			$message = array(
   				'status' => 'failed',
   				'message' => 'Invalid request',
   			);
			return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
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

$app->post('/testimonials/{pro_id}', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profileID = $request->getAttribute('pro_id');
		$comments = $request->getParam('testimonial');
		$User = Users::select('org_id')->where('id',$jwt)->first();

		$Profile = Profile::select('org_id','user_id')
						->where('id',$profileID)->first();

		if ($User['org_id'] == $Profile['org_id']){

			if ($Profile['user_id'] !== $jwt){

				
					$Testimonials = Testimonials::create([
   						'comments' => $comments,
   						'user_id' => $jwt,
   						'pro_id' => $profileID,
   						'status' => 1,
   						]);

					$message = array(
   						'status' => 'success',
   						'message' => 'Testimonial updated',
   					);
					return $response->withStatus(200)
    				->withHeader("Content-Type", "application/json")
    				->withJson($message);
			}
			else {

				$message = array(
   					'status' => 'failed',
   					'message' => 'Invalid data',
   				);
				return $response->withStatus(400)
    				->withHeader("Content-Type", "application/json")
    				->withJson($message);
			}
		}
		else {

			$message = array(
   				'status' => 'failed',
   				'message' => 'Invalid Data',
   			);
			return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
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

$app->put('/testimonials/{pro_id}/{id}', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$id = $request->getAttribute('id');
	$profileID = $request->getAttribute('pro_id');

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$Testimonials = Testimonials::where('id',$id)
					->where('user_id',$jwt)
					->where('pro_id',$profileID)
					->where('status',1)
					->first();

		if ($Testimonials) {

			$Testimonials->status = 0;
			$Testimonials->save();

			$message = array(
   				'status' => 'success',
   				'message' => 'Testimonial deleted',
   			);
			return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
    	}
    	else{

    		$message = array(
   				'status' => 'failed',
   				'message' => 'Invalid data',
   			);
		return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);

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