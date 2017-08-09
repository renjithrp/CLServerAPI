<?php

namespace Apps\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Apps\Models\Users;
class EmailAvailable extends AbstractRule
{

	public function Validate($input){

		return Users::where('email',$input)->count() == 0;
		
	}


}