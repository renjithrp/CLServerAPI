<?php

use Apps\Models\Users;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;

function GetRole($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

	if ($jwt){

		$UserRole = Users::select('role_id')
				->where('id',$jwt)
				->first();
		$role = Role::select('id as role_id', 'name as rolename','description')
				->where('id',$UserRole['role_id'])
				->where('status','1')
				->first();
		if ($role){

			return $m->data($response,$role);
		}
		else{
			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,'Invalid token');
	}
 }
