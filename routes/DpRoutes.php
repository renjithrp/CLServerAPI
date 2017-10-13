<?php

use Apps\Controllers\AmazonS3;
use Apps\Models\Profiledp;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;

function Uploaddp($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){


		$id = new Getid;
		$profileID = $id->profile($jwt)->first();

		$TempName =  basename($_FILES["dp"]['tmp_name']);
		$FileName = basename($_FILES["dp"]['name']);

		$extension = explode('.',$FileName);
		$extension = strtolower(end($extension));

		$key = md5(uniqid());

		$TempFileName = "{$key}.{$extension}";
		$TempFilePath = "../tmp/{$key}.{$extension}";

		$move = move_uploaded_file($_FILES["dp"]['tmp_name'], $TempFilePath);

		if ($_FILES["dp"]['tmp_name']){

			$size = getimagesize($TempFilePath);
		}

		if (@is_array($size)){

			#if (($size[0] !== 128) && ($size[1] !== 128)){

				return $m->error($response);

			#}
						
		}
		else {

			return $m->error($response);
		}

		if ($move) {

			$s3 = new AmazonS3;
			$s3->uploaddp($key,$TempFilePath);

			$dp = Profiledp::where('profile_id', $profileID)
					->where('status',1)->first();

			if ($dp){
				$dp->status = 0;
				$dp->save();
			}

			$updatedp = Profiledp::create([
				'dp' => $TempFileName,
				'profile_id' => $profileID,
				'status' => 1,

				]);

			if ($updatedp) {

				$data = [];
				$data['profile_id'] = $profileID;
				$data['dp'] = $s3->getdp($TempFileName);

				return $m->data($response,$data);
			}
			else{

				return $m->error($response);
			}
		}
		else {

			return $m->error($response);

		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}

function Getdp($request, $response, $args){


	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

	if ($jwt){

			$id = new Getid;
			$profileID = $id->profile($jwt)->first();

			#TempFilePath = '206cbadfef4eec165f5e1a9619ec6ec1.jpg';
			#$TempFilePath = $request->getAttribute('image');

			$dp = Profiledp::select('dp')
					->where('profile_id', $profileID)
					->where('status',1)
					->pluck('dp')
					->first();


			$s3 = new AmazonS3;
			$s3->getdp($dp);

			$data = [];
			$data['profile_id'] = $profileID;
			$data['dp'] = $s3->getdp($dp);

			return $m->data($response,$data);

	}
	else {

		return $m->failed($response,"Invalid token");
	}

}