<?php

use Apps\Models\Profile;
use Apps\Models\Users;
use Apps\Models\ProfileRating;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;

$app->get('/rating', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profile = Profile::select('id')
					->where('user_id',$jwt)
					->first();

		$count= ProfileRating::select('rating')
				->where('pro_id',$profile['id'])
				->count();

		if ($count) {

			$sum= ProfileRating::select('rating')
				->where('pro_id',$profile['id'])
				->sum('rating');

			$rating = (($sum/$count));
			$message = array(
   				'rating' => $rating,
   				'count' => $count,
   				);
			return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
    	}else {

    		$message = array(
   				'rating' => '0',
   				'count' => '0',
   			);
			return $response->withStatus(200)
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

$app->get('/rating/{pro_id}', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profileID = $request->getAttribute('pro_id');

		$sum= ProfileRating::select('rating')
				->where('pro_id',$profileID)
				->sum('rating');
		$count= ProfileRating::select('rating')
				->where('pro_id',$profileID)
				->count();
		if ($count) {

			$rating = (($sum/$count));
			$message = array(
   				'rating' => $rating,
   				'count' => $count,
   			);
			return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
    	}else{

    		$message = array(
   				'rating' => 0,
   				'count' => 0,
   			);
			return $response->withStatus(200)
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

$app->post('/rating/{pro_id}', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$profileID = $request->getAttribute('pro_id');
		$rating = $request->getParam('rating');
		$User = Users::select('org_id')->where('id',$jwt)->first();

		$Profile = Profile::select('org_id','user_id')
						->where('id',$profileID)->first();
		$min = 1;
		$max = 5;
		if ($User['org_id'] == $Profile['org_id'] && $rating <= $max && $min <= $rating){

			if ($Profile['user_id'] !== $jwt){

				$RatingExist = ProfileRating::where('user_id',$jwt)
							->where('pro_id',$profileID)
							->first();

				if($RatingExist){

					$RatingExist['rating'] = $rating;
					$RatingExist->save();
					$RatingOut = array('rating' => $RatingExist['rating'],);
					return $response->withStatus(200)
    						->withHeader("Content-Type", "application/json")
    						->withJson($RatingOut);
				}
				else{

					$Q = ProfileRating::create([
   						'rating' => $rating,
   						'user_id' => $jwt,
   						'pro_id' => $profileID,
   						]);

					if ($Q){

						$RatingOut = array('rating' => $rating,);
						return $response->withStatus(200)
    						->withHeader("Content-Type", "application/json")
    						->withJson($RatingOut);
					}
					else {

						$message = array(
   							'status' => 'eror',
   							'message' => 'unknown error',
   						);
						return $response->withStatus(500)
    						->withHeader("Content-Type", "application/json")
    						->withJson($message);
					}
				}
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