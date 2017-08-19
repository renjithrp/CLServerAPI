<?php

use Apps\Models\Profile;
use Apps\Models\Exams;
use Apps\Models\Notes;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\Token;

function Search($request, $response, $args) {

	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$id = new Getid;
		#$gname = new GetName;
		$orgId = $id->org($jwt)->first();
		$profileID = $id->profile($jwt)->first();

		#echo $orgId;
		#echo $profileID ;
		#exit;


		$query = $request->getAttribute('query');

		$name = explode(" ", $query);

		$count = count($name);

		$data = [];

						if ($count == 1){

							$profiles = Profile::where('profile.org_id',$orgId)
											->where('firstname','LIKE','%'.$name[0].'%')
											->orWhere('skills','LIKE','%'.$name[0].'%')
											->leftjoin('role','role.id','=','profile.role_id')
											->select('profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')

											->where('profile.status',1)
											->where('profile.id','!=',$profileID)
											->get();

							$exams = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
											->where('profile.org_id',$orgId)
											->where('exams.name','LIKE','%'.$query.'%')
											->orWhere('exams.description','LIKE','%'.$query.'%')
											->orWhere('profile.firstname','LIKE','%'.$name[0].'%')
											->rightjoin('subject','exams.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('exams.id as exam_id','exams.name as exam_name',
																'exams.description as exam_description','exams.duration','subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')	
											->where('exams.status',1)					
											->get();

							$notes = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
											->where('profile.org_id',$orgId)
											->where('notes.name','LIKE','%'.$name[0].'%')
											->orWhere('notes.description','LIKE','%'.$query.'%')
											->orWhere('profile.firstname','LIKE','%'.$name[0].'%')
											->rightjoin('subject','notes.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
																'subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')
											->where('notes.status',1)
											->get();
																					
								
						}
						elseif($count == 2){

							$profiles = Profile::where('profile.org_id',$orgId)
											->where('lastname','LIKE','%'.$name[1].'%')
											->OrWhere('firstname',$name[0])
											->orWhere('skills','LIKE','%'.$name[0].'%')
											->orWhere('skills','LIKE','%'.$name[1].'%')
											->leftjoin('role','role.id','=','profile.role_id')
											->select('profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')

											->where('profile.status',1)
											->where('profile.id','!=',$profileID)
											->get();;

							$exams = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
											->where('profile.org_id',$orgId)
											->where('exams.name','LIKE','%'.$query.'%')
											->orWhere('exams.description','LIKE','%'.$query.'%')
											->orWhere('firstname',$name[0])
											->orWhere('lastname','LIKE','%'.$name[1].'%')
											->rightjoin('subject','exams.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('exams.id as exam_id','exams.name as exam_name',
																'exams.description as exam_description','exams.duration','subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')
											->where('exams.status',1)	
											->get();

							$notes = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
											->where('profile.org_id',$orgId)
											->where('notes.name','LIKE','%'.$query.'%')
											->orWhere('notes.description','LIKE','%'.$query.'%')
											->orWhere('firstname',$name[0])
											->orWhere('lastname','LIKE','%'.$name[1].'%')
											->rightjoin('subject','notes.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
																'subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')
											->where('notes.status',1)
											->get();
						}
						else {

							$profiles = Profile::where('profile.org_id',$orgId)
											->where('lastname','LIKE',$name[1].' '.$name[2].'%')
											->OrWhere('firstname',$name[0])
											->orWhere('skills','LIKE','%'.$name[0].'%')
											->orWhere('skills','LIKE','%'.$name[1].'%')
											->orWhere('skills','LIKE','%'.$query.'%')
											->leftjoin('role','role.id','=','profile.role_id')
											->select('profile.id as profile_id','profile.firstname','profile.lastname',
																			'profile.role_id','role.name as role_name')

											->where('profile.status',1)
											->where('profile.id','!=',$profileID)
											->orderBy('profile.lastname','DESC')
											->get();

							$exams = Exams::rightjoin('profile','profile.user_id','=','exams.user_id')
											->where('profile.org_id',$orgId)
											->where('exams.name','LIKE','%'.$query.'%')
											->orWhere('exams.description','LIKE','%'.$query.'%')
											->orWhere('firstname',$name[0])
											->orWhere('lastname','LIKE',$name[1].' '.$name[2].'%')
											->rightjoin('subject','exams.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('exams.id as exam_id','exams.name as exam_name',
																'exams.description as exam_description','exams.duration','subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')
											->where('exams.status',1)
											->orderBy('profile.lastname','DESC')
											->get();

							$notes = Notes::rightjoin('profile','profile.user_id','=','notes.user_id')
											->where('profile.org_id',$orgId)
											->where('notes.name','LIKE','%'.$query.'%')
											->orWhere('notes.description','LIKE','%'.$query.'%')
											->orWhere('firstname',$name[0])
											->orWhere('lastname','LIKE',$name[1].' '.$name[2].'%')
											->rightjoin('subject','notes.sub_id','=','subject.id')
											->rightjoin('section','subject.sec_id','=','section.id')
											->rightjoin('role','role.id','=','profile.role_id')
											->select('notes.id as note_id','notes.name as note_name','notes.description as note_description','notes.file',
																'subject.id as subj_id,','subject.name as subj_name',
																'section.id as sec_id','section.name as sec_name',
																'profile.id as profile_id','profile.firstname','profile.lastname',
																'profile.role_id','role.name as role_name')
											->where('notes.status',1)
											->orderBy('profile.lastname','DESC')
											->get();
						}


						$data['profiles'] = $profiles;
						$data['exams'] = $exams;
						$data['notes'] = $notes;

						return $m->data($response,$data);
		}
		else {

		return $m->failed($response,"Invalid token");
	}

						#return $response->withJson($data) ;


}