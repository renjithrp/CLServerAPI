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

				$profile = Profile::leftjoin('role', 'profile.role_id','=','role.id')
					->select('profile.id as profile_id','user_id','role_id','firstname','lastname',
						'dp','role.name as role' )
					->where('profile.org_id',$org['org_id'])
					->where('role_id','!=','101')
					->where('profile.status','1')
					->get();

		return $response->withJson($profile);
	}

 });

$app->get('/profile/{id}', function ($request, $response, $args) {
	
	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);

	$id = $request->getAttribute('id');

	$org = Users::select('org_id')->where('id',$jwt)->first();

	$Organization = Profile::select('firstname as organization')
						->where('role_id','101')
						->where('id',$org['org_id'])->pluck('organization');

	$profile = Profile::leftjoin('role', 'profile.role_id','=','role.id')
					->select('profile.*','role.name as role' )
					->where('role_id','!=','101')
					->where('profile.id',$id)
					->first();

	return $response->withJson($profile);

 });
