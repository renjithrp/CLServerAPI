<?php

use Apps\Models\Verification;
use Apps\Controllers\Messages as m;

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

		return $m->data($response,$data);
	}
	else{

		return $m->failed($response,'Invalid verification code');
	}
 }  