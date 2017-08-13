<?php

use Apps\Models\Verification;
use Apps\Controllers\Messages as m;

function VeryfyEmail($request, $response, $args){

	$code = $request->getParams('code');
	$email = $request->getParams('email');


	$m = new m;

	$result = Verification::where('status',1)
		->where('email',$email)
		->where('code',$code)
		->first();

	if ($result){
		

		$result->status = 0;
		$result->save();
		return $m->success($response,'Verification completed');
	}
	else{

		return $m->failed($response,'Invalid verification code');
	}
 }  