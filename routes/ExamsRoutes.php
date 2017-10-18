<?php

use Apps\Models\Sections;
use Apps\Models\Subject;
use Apps\Models\Exams;
use Apps\Models\Marks;
use Apps\Models\Qustions;
use Apps\Models\Answers;
use Apps\Controllers\Token;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;

function AttendExam($request, $response, $args) {

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

		$duration = Exams::select('duration')
					->where('status',1)
					->where('published',1)
					->where('id',$examID)
					->pluck('duration')
					->first();
		
		$duration = ($duration + 1);

		//Get Exam token
		$server = $request->getServerParams();
		unset($server['HTTP_AUTHORIZATION']);

  		$now = new DateTime();
		$future = new DateTime("now +$duration minutes");

    	$examToken = $token->examtoken($server,$now,$future);

		if (($role[0] == '103')){


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
											'exams.description as exam_description','exams.duration',
											'profile.id as profile_id','profile.firstname as user_firstname','profile.lastname as user_lastname',
											'exams.created_at','exams.updated_at')
										->where('exams.sub_id',$subjID)
										->where('exams.status',1)
										->where('exams.id',$examID)->first();
				
				$data[$a][$b]['exam']['qustions'] = Qustions::select('id','question','exam_id','created_at','updated_at')

									->where('qustion.exam_id',$examID)->get();
				$data[$a][$b]['exam']['security'] = $examToken;

				foreach($data[$a][$b]['exam']['qustions'] as $q){

					$q->answers =  Answers::select('id as ans_id', 'answer')
									->where('qust_id',$q['id'])
									->where('status','1')
									->get();			
				}


				if($data){	

					$marks = Marks::create([
						'exam_id' => $examID,
   						'user_id' => $jwt,
   						'marks'  => 0,

						]);

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

function PostExam($request, $response, $args) {


	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$examAuth = $request->getHeader('HTTP_EXAM');
		$server = $request->getServerParams();

		$ewt = $token->validateExam($examAuth);

		if ($ewt){

			//Qustions and answers from db

			$answers = $request->getParsedBody();
			$examID = $request->getAttribute('exam_id');
			$marks = 0;
			foreach ($answers as $qa) {

				$q = $qa['qust_id'];
				$a = $qa['ans_id'];

				$verify = Answers::where('qust_id',$q)
					->where('id',$a)
					->where('flag',1)
					->first();

				if ($verify) {

					$marks = 	$marks + 1;
				} 
			}
	
			$id = new Getid;

			$update = Marks::where('exam_id',$examID)
				->where('mark',0)
				->where('user_id',$jwt)
				->orderBy('created_at', 'DESC')
				->first();
			$update->mark = $marks;
			$update->save();

			$messages = array('marks' => $marks);

			return $m->data($response,$messages);
		}
		else{
			return $m->failed($response,"Invalid exam token");
		}
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}

function CreateExam($request, $response, $args) {

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
		$role = $id->role($jwt);

		if (($role[0] == '102')){

			$exams = Exams::create([

				'name' => $request->getParam('exam_name'),
				'description' => $request->getParam('exam_description'),
				'sub_id' => (int)$subjID,
				'duration' => (int)$request->getParam('duration'),
				'published' => (int)$request->getParam('published'),
				'user_id' => $jwt,
				'sec_id' => (int)$secID,
				'status' => 1
				]);

			if ($exams){
				
				return $m->data($response,$exams);
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

function UpdateExam($request, $response, $args) {

$token = new Apps\Controllers\Token;
$security = $request->getHeader('authorization');
$jwt = $token->validate($security);
//if the token is valid it will return UserID
$m = new m;
if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');
		$examID = $request->getAttribute('exam_id');

		$id = new Getid;
		$orgId = $id->org($jwt);
		$role = $id->role($jwt);

		if (($role[0] == '102')){

			$exams = Exams::where('id',$examID)
				->where('user_id',$jwt)->first();

			
			if ($exams){

				$exams['name'] = $request->getParam('exam_name');
				$exams['description'] =  $request->getParam('exam_description');
				$exams['published'] =  (int)$request->getParam('published');
				$exams['duration'] =  (int)$request->getParam('duration');

				$exams->save();

				return $m->data($response,$exams);
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

function CreateQustions($request, $response, $args) {

$token = new Apps\Controllers\Token;
$security = $request->getHeader('authorization');
$jwt = $token->validate($security);
//if the token is valid it will return UserID
$m = new m;
if ($jwt){

		$secID = $request->getAttribute('sec_id');
		$subjID = $request->getAttribute('subj_id');
		$examID = $request->getAttribute('exam_id');

		$id = new Getid;
		$orgId = $id->org($jwt);
		$role = $id->role($jwt);

		if (($role[0] == '102')){

			$question = Qustions::create([

				'question' => $request->getParam('question'),
				'exam_id' => $examID,
				'status' => 1
				]);

			$answers = $request->getParam('answers');

			if ($question){
			
				foreach ($answers as $ans) {
				
					$a = Answers::create([

						'answer' => $ans['answer'],
						'flag' => $ans['flag'],
						'qust_id' => $question->id,
						'status' => 1

					]);
				}
			}
			else{
				return $m->error($response);
			}
			
			if ($a ){
				
				$question['answers'] = $answers;
				return $m->data($response,$question);
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
