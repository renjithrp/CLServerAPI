<?php

namespace Apps\Controllers;

class Messages {


	function success($response,$msg){

		$message = array(
   				'status' => 'success',
   				'message' => $msg,
   			);

		return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
		
	}

	function failed($response,$msg){


		$message = array(
   				'status' => 'failed',
   				'message' => $msg,
   			);
		return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
	}

	function error($response){

		$message = array(
   				'status' => 'failed',
   				'message' => 'Invalid request',
   			);
		return $response->withStatus(500)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
	}

	function data($response,$data){

		return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($data);

	}

}