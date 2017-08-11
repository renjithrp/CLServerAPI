<?php

namespace Apps\Controllers;

class Messages {


	function success($response,$msg){

    $message = array();

		$message['response_status']  = array(
   				'status' => 'success',
   				'message' => $msg,
   			);
    $message['response_data'] = array();
		return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
		
	}

	function failed($response,$msg){

    $message = array();
		$message['response_status'] = array(
   				'status' => 'failed',
   				'message' => $msg,
   			);
    $message['response_data'] = array();
		return $response->withStatus(400)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
	}

	function error($response){
    $message = array();

		$message['response_status']  = array(
   				'status' => 'failed',
   				'message' => 'Invalid request',
   			);
    $message['response_data'] = array();
		return $response->withStatus(500)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
	}

	function data($response,$data){

    $message['response_status'] = array(
          'status' => 'success',
        );
    $message['response_data'] = $data;
		return $response->withStatus(200)
    			->withHeader("Content-Type", "application/json")
    			->withJson($message);
	}
  
}