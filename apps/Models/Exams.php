<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Exams extends Eloquent
{
	protected $table = 'exams';
	protected $fillable = ['name','description', 'duration', 'status', 'sub_id', 'user_id','published' ,'created_at'];
}