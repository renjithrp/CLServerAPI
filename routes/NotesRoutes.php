<?php
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\AmazonS3;
use Apps\Controllers\Token;
use Apps\Models\Notes;
use Apps\Models\Sections;
use Apps\Models\Subject;

function CreateNotes($request, $response, $args){

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

			$notes = Notes::create([
				'name' => $request->getParam('note_name'),
				'description' => $request->getParam('note_description'),
				'user_id' => $jwt,
				'sub_id' => $subjID,
				'status' => 1
				]);

			if ($notes){

				$data = array('sec_id' => (int)$secID,
					'subj_id' => (int)$subjID,
					'note_id' => $notes['id']);

				return $m->data($response,$data);
			}
			else{

				$m->error($response);
			}
		}
		else{
			$m->error($response);
		}
	}
	else{

		return $m->failed($response,"Invalid token");
	}

}