<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Notes extends Eloquent
{
	protected $table = 'notes';
	protected $fillable = ['name', 'description', 'user_id', 'sub_id', 'status'];
}