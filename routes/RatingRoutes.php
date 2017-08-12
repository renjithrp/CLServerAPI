<?php

use Apps\Models\Profile;
use Apps\Models\Users;
use Apps\Models\ProfileRating;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;

function GetRating($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

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
			return $m->data($response,$message);

    	}else {

    		$message = array(
   				'rating' => '0',
   				'count' => '0',
   			);

			return $m->data($response,$message);
    	}
	}	
	else {

		return $m->failed($response,'Invalid token');
	}
 }

function GetUserRating($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

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
			return $m->data($response,$message);

    	}else{

    		$message = array(
   				'rating' => 0,
   				'count' => 0,
   			);
			return $m->data($response,$message);
    	}
	}
	else {

		return $m->failed($response,'Invalid token');
	}
 }

function PostRating($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

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

					return $m->data($response,$RatingOut);
				}
				else{

					$Q = ProfileRating::create([
   						'rating' => $rating,
   						'user_id' => $jwt,
   						'pro_id' => $profileID,
   						]);

					if ($Q){

						$RatingOut = array('rating' => $rating,);
						return $m->data($response,$RatingOut);
					}
					else {

						return $m->error($response);
					}
				}
			}
			else {

				return $m->error($response);
			}
		}
		else {

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,'Invalid token');
	}
 }