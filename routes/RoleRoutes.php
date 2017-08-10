<?php

use Apps\Models\Users;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;

$app->get('/role', function ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$UserRole = Users::select('role_id')
				->where('id',$jwt)
				->first();
		$role = Role::select('id as role_id', 'name as rolename','description')
				->where('id',$UserRole['role_id'])
				->where('status','1')
				->first();
		return $response->withJson($role);

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
