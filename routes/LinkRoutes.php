
<?php

use Apps\Models\Users;
use Apps\Models\Assocsection;
use Apps\Models\Profile;
use Apps\Models\Sessions;
use Apps\Models\Sections;
use Apps\Models\Subject;
use Apps\Models\Role;
use Respect\Validation\Validator as v;
use Apps\Controllers\Token;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;
use Apps\Controllers\Messages as m;

function UpdateLinkProfile ($request, $response, $args) {

	$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$id = new Getid;
		$role = $id->role($jwt)->first();
		$orgId = $id->org($jwt)->first();
		$profileID = $id->profile($jwt)->first();
		$sec_id = $request->getParam('sec_id');
		

		if ($role == 102) {

			$subj_id = $request->getParam('subj_id');

			$links = Assocsection::where('pro_id', $profileID)
				->where('sec_id',$sec_id)
				->where('sub_id',$subj_id)
				->first();
		}
		elseif ($role == 103){
			$subj_id = '';
			$links = Assocsection::where('pro_id', $profileID)
				->first();
		}
		else{
			$m->error($response);
		}

		if($links){

			$status = $request->getParam('status');

			if (!$status) {

				$links->sec_id = $sec_id;
				$links->sub_id = $subj_id;
				$links->status = 1;
				$links->save();

				if ($role == 102){
					$data = array();
					$dt = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->get();
					$data['sections'] = $dt->unique('sec_id');

					#return $m->data($response,$dt->unique('sec_id'));

					foreach ($data['sections'] as $sections) {
						$sectionID = $sections['sec_id'];
						$sections->subjects = Assocsection::rightjoin('subject', 'assocsection.sub_id','=','subject.id')
									->where('assocsection.pro_id',$profileID)
									->where('assocsection.sec_id',$sectionID)
									->where('assocsection.status',1)
									->select('sub_id as subj_id','subject.name')
									->get();

					}
				}
				elseif ($role == 103){

						$data['sections'] = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->first();

				}

				return $m->data($response,$data);
			}
			elseif ($status == "DELETE") {


				$links->status = 0;
				$links->save();

				if ($role == 102){

					$data = array();
					$dt = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->get();	

					$data['sections'] = $dt->unique('sec_id');

					foreach ($data['sections'] as $sections) {
						$sectionID = $sections['sec_id'];
						$sections->subjects = Assocsection::rightjoin('subject', 'assocsection.sub_id','=','subject.id')
									->where('pro_id',$profileID)
									->where('assocsection.sec_id',$sectionID)
									->where('assocsection.status',1)
									->select('sub_id as subj_id','subject.name')
									->get();

					}
				}
				elseif ($role == 103){

							$data['sections'] = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->first();
				}
				return $m->data($response,$data);
			} 
			else{

				$m->error($response);
			}
		}
		else{

			if ($role == 102) {

				$links = Assocsection::create([

				'pro_id' => $profileID,
				'sec_id' => $sec_id,
				'sub_id' => $subj_id,
				'status' => 1,

				]);

				$data = array();
				$dt = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->get();

				$data['sections'] = $dt->unique('sec_id');

				foreach ($data['sections'] as $sections) {
					$sectionID = $sections['sec_id'];
					$sections->subjects = Assocsection::rightjoin('subject', 'assocsection.sub_id','=','subject.id')
									->where('pro_id',$profileID)
									->where('assocsection.sec_id',$sectionID)
									->where('assocsection.status',1)
									->select('sub_id as subj_id','subject.name')
									->get();

				}
			}

			elseif ($role == 103){

				$links = Assocsection::create([

				'pro_id' => $profileID,
				'sec_id' => $sec_id,
				'sub_id' => '0',
				'status' => 1, ]);

				$data['sections'] = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->first();

			}

			if($links){


				return $m->data($response,$data);
			}

			$m->error($response);
		}

		
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


function GetLinkProfile ($request, $response, $args) {

$server = $request->getServerParams();
	$token = new Apps\Controllers\Token;
	$security = $request->getHeader('authorization');
	$jwt = $token->validate($security);
	//if the token is valid it will return UserID
	$m = new m;
	if ($jwt){

		$id = new Getid;
		$role = $id->role($jwt)->first();
		$orgId = $id->org($jwt)->first();
		$profileID = $id->profile($jwt)->first();

		if ($role == 102){

			$data = array();
			$dt = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->get();

			$data['sections'] = $dt->unique('sec_id');

			foreach ($data['sections'] as $sections) {
					$sectionID = $sections['sec_id'];
					$sections->subjects = Assocsection::rightjoin('subject', 'assocsection.sub_id','=','subject.id')
									->where('pro_id',$profileID)
									->where('assocsection.sec_id',$sectionID)
									->where('assocsection.status',1)
									->select('sub_id as subj_id','subject.name')
									->get();

			}

		}
		elseif ($role == 103){

				$data['sections'] = Assocsection::rightjoin('section', 'assocsection.sec_id','=','section.id')
									->where('pro_id',$profileID)
									->select('sec_id','section.name')
									->where('assocsection.status',1)
									->first();

		}
		else {
			
			$m->error($response);

		}
		return $m->data($response,$data);
		

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