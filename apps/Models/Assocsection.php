<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Assocsection extends Eloquent
{
	protected $table = 'assocsection';
	protected $fillable = ['pro_id', 'sec_id', 'sub_id','status'];
}