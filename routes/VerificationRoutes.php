<?php

use Apps\Models\Verification;
use Apps\Controllers\Messages as m;
use Apps\Models\Users;
use Apps\Models\Sessions;

function VeryfyEmail($request, $response, $args){

	$code = $request->getParams('code');
	$email = $request->getParams('email');

	$server = $request->getServerParams();
	$now = new DateTime();
	$future = new DateTime("now +5 hours");


	$m = new m;

	$result = Verification::where('status',1)
		->where('email',$email)
		->where('code',$code)
		->orderBy('created_at', 'ASC')
		->first();

	if ($result){

		$token = new Apps\Controllers\Token;
    	$data = $token->create($server,$now,$future);


		$result->status = 0;
		$result->save();

		$user = Users::where('email',$email)->first();
		$status = True;

		$session = Sessions::create([
   			  'token' => $data['token'],
   			  'user_id' => $user->id,
   			  'created_at' => $now,
   			  'valid_till' => $future,
   			  'status' => $status,
   		   ]);

		$userinfo = array('profile_id' => Null,
        	'firstname' => Null,
        	'lastname' => Null,
    		'email' => $user->email,
    		'role_id' => $user->role_id,
    		'org_id' => $user->org_id,
    		'secure' => $data,
    		);
		
		return $m->data($response,$userinfo);
	}
	else{

		return $m->failed($response,'Invalid verification code');
	}
 }  