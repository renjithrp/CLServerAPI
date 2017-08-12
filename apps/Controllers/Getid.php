<?php

namespace Apps\Controllers;
use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Role;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;


class Getid{

	function org($uid){

		$uid = Users::select('org_id')
			->where('id',$uid)
			->pluck('org_id');
		return $uid;
	}
	function profile($uid){

		$pid = Profile::select('id')
				->where('user_id',$uid)
				->pluck('id');

		if(count($pid) == 0){

          $pid = Null;
        }

		return $pid;
	}
	function role($uid){

		$rid = Users::select('role_id')
			->where('id',$uid)
			->pluck('role_id');

		return $rid;
	}
}
