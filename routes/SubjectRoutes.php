
<?php

use Apps\Models\Sections;
use Apps\Models\Notes;
use Apps\Models\Subject;
use Apps\Models\Exams;
use Apps\Models\Qustions;
use Apps\Models\Answers;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;
use Respect\Validation\Validator as v;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;

function CreateSubjects($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){


		$id = new Getid;
		$gname = new GetName;
		$orgId = $id->org($jwt)->first();
		$role = $id->role($jwt)->first();
		$secID = $request->getAttribute('sec_id');

		$validation = new Apps\Validation\Validator;

		$validation->validate($request, [
		  'subj_name' => v::notEmpty(),
		  'subj_description' => v::notEmpty(),

	   	]);

	   	if ($validation->failed()){
	
			$errors = array('status' => 'failed',
			'message' => $_SESSION['errors'],

			);
			unset($_SESSION['errors']);

			$message = array();
    		$message['response_status'] = $errors;
    		$message['response_data'] = array();
    		return $response->withStatus(200)
          		->withHeader("Content-Type", "application/json")
          		->withJson($message);
		}


		if (($role == 101) || ($role == 102)){


			$sections = Sections::where('org_id',$orgId)
							->where('status', '1')
							->where('id',$secID)
							->first();

			if (!$sections){

				return $m->error($response);

			}

			$subject = Subject::create([

				'name' => $request->getParam('subj_name'),
				'description' => $request->getParam('subj_description'),
				'user_id' => $jwt,
				'sec_id' => $secID,
				'status' => '1'
				]);
			

			if ($subject){

				$data = array('subj_id' => $subject['id'],
					'sec_id' =>  (int)$secID);

					return $m->data($response,$data);
			}
			else{

				return $m->error($response);
			}
		}
		else{

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}



function UpdateSubjects($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$id = new Getid;
		$gname = new GetName;
		$orgId = $id->org($jwt)->first();
		$role = $id->role($jwt)->first();
		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');

		$validation = new Apps\Validation\Validator;

		$validation->validate($request, [
		  'subj_name' => v::notEmpty(),
		  'subj_description' => v::notEmpty(),

	   	]);

	   	if ($validation->failed()){
	
			$errors = array('status' => 'failed',
			'message' => $_SESSION['errors'],

			);
			unset($_SESSION['errors']);

			$message = array();
    		$message['response_status'] = $errors;
    		$message['response_data'] = array();
    		return $response->withStatus(200)
          		->withHeader("Content-Type", "application/json")
          		->withJson($message);
		}

		if (($role == 101) || ($role == 102)){


			$sections = Sections::where('org_id',$orgId)
					->where('status', '1')
					->where('id',$secID)
					->first();

			if (!$sections){

				return $m->error($response);

			}


			$subject = Subject::where('id',$subjID)
				->where('user_id',$jwt)->first();

			if ($subject){

				$subject['name'] = $request->getParam('subj_name');
				$subject['description'] =  $request->getParam('subj_description');

				$subject->save();

				$data = array('subj_id' => $subject['id'],
					'sec_id' => $subject['sec_id']);
				return $m->data($response,$data);
			}
			else{

				return $m->error($response);
			}
		}
		else{

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}