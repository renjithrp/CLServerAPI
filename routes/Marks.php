
<?php

use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Sessions;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;
use Apps\Models\Exams;
use Apps\Models\Marks;

function GetMarks ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID

	if ($jwt){

		$id = new Getid;
		$orgId = $id->org($jwt)->first();
		$role = $id->role($jwt)->first();
		$profileID = $request->getAttribute('pro_id');

		$m = new m;

		
		$Marks = Marks::rightjoin('exams','marks.exam_id','=','exams.id')
					->rightjoin('subject','exams.sub_id','=','subject.id')
					->rightjoin('profile','profile.user_id','=','marks.user_id')
					->where('profile.id',$profileID)
					->select('exams.id as exam_id','exams.name as exam_name','subject.id as subj_id','subject.name as subj_name','mark')
					->get();
					
		;
		return $m->data($response,$Marks);

		

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

 };
