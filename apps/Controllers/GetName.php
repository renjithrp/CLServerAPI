<?php

namespace Apps\Controllers;
use Apps\Models\Users;
use Apps\Models\Profile;
use Apps\Models\Role;


class GetName{

	function name($uid){
	
		$name = Profile::select('firstname','lastname')
				->where('user_id',$uid)
				->first();
		
		return $name;

	}

	function role($uid){



	}

	function section($sid){



	}

	function subject($subid){



	}

	function notes($nid){



	}

	function exams($eid){



	}


}