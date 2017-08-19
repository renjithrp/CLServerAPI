
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



function CreateSections($request, $response, $args) {

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

		$validation = new Apps\Validation\Validator;

		$validation->validate($request, [
		  'sec_name' => v::notEmpty(),
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


		if ($role == 101){

			$sections = Sections::create([
				'name' => $request->getParam('sec_name'),
				'description' => $request->getParam('sec_description'),
				'user_id' => $jwt,
				'org_id' => $orgId,
				'status' => '1'
				]);

			if ($sections){
				$data = array('sec_id' => $sections['id']);
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

function UpdateSections($request, $response, $args) {

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
		  'sec_name' => v::notEmpty(),
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


		if ($role == 101){

			$sections = Sections::where('user_id',$jwt)
							->where('org_id',$orgId)
							->where('status', '1')
							->where('id',$secID)
							->first();


			if ($sections){

				$sections['name'] = $request->getParam('sec_name');
				$sections['description'] = $request->getParam('sec_description');
				$sections->save();

				$data = array('sec_id' => $sections['id']);
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

function GetSections($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$id = new Getid;
		$gname = new GetName;
		$orgId = $id->org($jwt);

		$a = 'organization';
		$data=array();
		$data[$a]['org_id'] = $orgId[0];
		$data[$a]['org_name'] = $gname->name($jwt)['firstname'];
		$data[$a]['sections'] = Sections::rightjoin('profile',
											'profile.user_id','=','section.user_id')
										->select('section.id as sec_id','section.name as sec_name','section.description as sec_description',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'section.created_at','section.updated_at')
										->where('section.org_id',$orgId)
										->where('section.status',1)
										->orderBy('section.id', 'ASC')
										->get();
		if($data){

			return $m->data($response,$data);
		}
		else {
			$m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
 }

//Inside sections -> subjects

function GetSubjects ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$id = new Getid;
		$orgId = $id->org($jwt);

		$section = Sections::select('id as sec_id', 'name as sec_name')
				->where('id',$secID)
				->where('org_id',$orgId)->first();
		if ($section){

			$a = 'section';
			$data=array();
			$data[$a] = $section;
			$data[$a]['subjects'] = Subject::rightjoin('profile','profile.user_id','=','subject.user_id')
				->select('subject.id as subj_id','subject.name as subj_name','subject.description as subj_description',
					'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
					'subject.created_at','subject.updated_at')
				->where('subject.sec_id',$secID)
				->where('subject.status',1)
				->orderBy('subject.id', 'ASC') 
				->get();

			if($data){	

				return $m->data($response,$data);
			}
			else {
				$m->error($response);
			}
		}
		else {

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
};

function GetAllNotes ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;

	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');

		$id = new Getid;
		$orgId = $id->org($jwt);

		$section = Sections::select('id as sec_id', 'name as sec_name')
				->where('id',$secID)
				->where('org_id',$orgId)->first();

		$subject = Subject::select('id as subj_id', 'name as subj_name')
				->where('id',$subjID)
				->where('sec_id',$secID)->first();

		if (($section) && ($subject)){

			$a = "section";
			$b = "subject";
			$data = array();
			$data[$a]= $section;
			$data[$a][$b] = $subject;
			$data[$a][$b]['notes'] = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
										->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'notes.created_at','notes.updated_at')
										->where('notes.sub_id',$subjID)
										->where('notes.status',1)
										->orderBy('notes.created_at', 'DESC')
										->get();
			if($data){	

				return $m->data($response,$data);
			}
			else {
				$m->error($response);
			}
		}
		else {

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
};

function GetAllExams ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');

		$id = new Getid;
		$orgId = $id->org($jwt);

		$section = Sections::select('id as sec_id', 'name as sec_name')
				->where('id',$secID)
				->where('org_id',$orgId)->first();

		$subject = Subject::select('id as subj_id', 'name as subj_name')
				->where('id',$subjID)
				->where('sec_id',$secID)->first();

		if (($section) && ($subject)){

			$a = "section";
			$b = "subject";
			$data = array();
			$data[$a]= $section;
			$data[$a][$b] = $subject;
			$data[$a][$b]['exams'] = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
										->select('exams.id as exam_id','exams.name as exam_name',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'exams.description as exam_description','exams.created_at','exams.updated_at')
										->where('exams.sub_id',$subjID)
										->where('exams.status',1)
										->orderBy('exams.created_at', 'DESC')
										->get();

			if($data){	

				return $m->data($response,$data);
			}
			else {

				$m->error($response);
			}
		}
		else {

			return $m->error($response);
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
};

function GetNotesAndExams ($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');

		$id = new Getid;
		$orgId = $id->org($jwt);

		$section = Sections::select('id as sec_id', 'name as sec_name')
				->where('id',$secID)
				->where('org_id',$orgId)->first();

		$subject = Subject::select('id as subj_id', 'name as subj_name')
				->where('id',$subjID)
				->where('sec_id',$secID)->first();

		if (($section) && ($subject)){

			$a = "section";
			$b = "subject";
			$data = array();
			$data[$a]= $section;
			$data[$a][$b] = $subject;
			$data[$a][$b]['exams'] = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
										->select('exams.id as exam_id','exams.name as exam_name',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'exams.description as exam_description','exams.created_at','exams.updated_at')
										->where('exams.sub_id',$subjID)
										->where('exams.status',1)
										->orderBy('exams.created_at', 'DESC')
										->get();

			$data[$a][$b]['notes'] = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
										->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'notes.created_at','notes.updated_at')
										->where('notes.sub_id',$subjID)
										->where('notes.status',1)
										->orderBy('notes.created_at', 'DESC')
										->get();

			if($data){	

				return $m->data($response,$data);
			}
			else {

				$m->error($response);
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

function GetNote($request, $response, $args){

$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');
		$noteID = $request->getAttribute('note_id');

		$id = new Getid;
		$orgId = $id->org($jwt);

		$section = Sections::select('id as sec_id', 'name as sec_name')
				->where('id',$secID)
				->where('org_id',$orgId)->first();

		$subject = Subject::select('id as subj_id', 'name as subj_name')
				->where('id',$subjID)
				->where('sec_id',$secID)->first();

		if (($section) && ($subject)){

			$a = "section";
			$b = "subject";
			$data = array();
			$data[$a]= $section;
			$data[$a][$b] = $subject;
			$data[$a][$b]['note'] = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
										->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'notes.created_at','notes.updated_at')
										->where('notes.sub_id',$subjID)
										->where('notes.id',$noteID)
										->where('notes.status',1)
										->orderBy('notes.created_at', 'DESC')
										->get();

			if($data){	

				return $m->data($response,$data);
			}
			else {

				$m->error($response);
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

function GetExam($request, $response, $args){

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');
		$noteID = $request->getAttribute('note_id');
		$examID = $request->getAttribute('exam_id');

		$id = new Getid;
		$orgId = $id->org($jwt);
		$role = $id->role($jwt);


		if (($role[0] == '101') || ($role[0] == '102')){


			$section = Sections::select('id as sec_id', 'name as sec_name')
					->where('id',$secID)
					->where('org_id',$orgId)->first();

			$subject = Subject::select('id as subj_id', 'name as subj_name')
					->where('id',$subjID)
					->where('sec_id',$secID)->first();
			

			if (($section) && ($subject)){

				$a = "section";
				$b = "subject";
				$data = array();
				$data[$a]= $section;
				$data[$a][$b] = $subject;

				$data[$a][$b]['exam'] = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
										->select('exams.id as exam_id','exams.name as exam_name',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'exams.description as exam_description','exams.created_at','exams.updated_at')
										->where('exams.sub_id',$subjID)
										->where('exams.status',1)
										->where('exams.id',$examID)->first();
				
				$data[$a][$b]['exam']['qustions'] = Qustions::where('qustion.exam_id',$examID)->get();

				foreach($data[$a][$b]['exam']['qustions'] as $q){

					$q->answers =  Answers::select('id as ans_id', 'answer','flag')
									->where('qust_id',$q['id'])
									->where('status','1')
									->get();			
					}

				if($data){	

					return $m->data($response,$data);
				}
				else {

					$m->error($response);
				}
			}
			else {

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