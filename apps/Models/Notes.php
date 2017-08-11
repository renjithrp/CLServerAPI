<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Notes extends Eloquent
{
	protected $table = 'notes';
	protected $fillable = ['email', 'password', 'role_id', 'org_id', 'status', 'created_at'];
}