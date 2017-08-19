<?php

use Apps\Controllers\AmazonS3;

function Uploaddp($request, $response, $args) {



	#$s3 = new AmazonS3;
	#echo $s3->uploaddp('sss','sss');

	$TempName =  basename($_FILES["fileToUpload"]['tmp_name']);
	$FileName = basename($_FILES["fileToUpload"]['name']);

	$extension = explode('.',$FileName);
	$extension = strtolower(end($extension));

	$key = md5(uniqid());

	$TempFileName = "{$key}.{$extension}";
	$TempFilePath = "../tmp/{$key}.{$extension}";

	$move = move_uploaded_file($_FILES["fileToUpload"]['tmp_name'], $TempFilePath);

	if ($move) {

		$s3 = new AmazonS3;
		$s3->uploaddp($key,$TempFilePath);
	}
	else {


	}
}

function Getdp($request, $response, $args){


	#TempFilePath = '206cbadfef4eec165f5e1a9619ec6ec1.jpg';
	$TempFilePath = $request->getAttribute('image');
	$s3 = new AmazonS3;
	$s3->getdp($TempFilePath);

}