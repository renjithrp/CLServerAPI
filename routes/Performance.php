<?php
use Apps\Models\Sections;
use Apps\Models\Subject;
use Apps\Models\Exams;
use Apps\Models\Marks;
use Apps\Models\Qustions;
use Apps\Models\Answers;
use Apps\Models\Performance;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;



function Performance($request, $response, $args) {

$token = new Apps\Controllers\Token;
$security = $request->getHeader('authorization');
$jwt = $token->validate($security);
//if the token is valid it will return UserID
$m = new m;
if ($jwt){

		$id = new Getid;
		$orgId = $id->org($jwt);
		$profileID = $id->profile($jwt)->first();

		$Performance = Performance::select('performance')
					->where('profile_id',$profileID)
					->where('org_id',$orgId)
					->orderBy('id', 'desc')->first();

		if (!$Performance){

			$Performance['performance'] = 0;
		}

		return $m->data($response,$Performance);

		
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}

function ProPerformance($request, $response, $args) {

$token = new Apps\Controllers\Token;
$security = $request->getHeader('authorization');
$jwt = $token->validate($security);
//if the token is valid it will return UserID
$m = new m;
if ($jwt){

		$id = new Getid;
		$orgId = $id->org($jwt);
		$profileID = $request->getAttribute('pro_id');

		$Performance = Performance::select('performance')
					->where('profile_id',$profileID)
					->where('org_id',$orgId)
					->orderBy('id', 'desc')->first();

		if (!$Performance){

			$Performance['performance'] = 0;
		}

		return $m->data($response,$Performance);

		
	}
	else {

		return $m->failed($response,"Invalid token");
	}
}