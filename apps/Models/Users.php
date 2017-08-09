<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Users extends Eloquent
{
	#protected $table = 'cldb_user';
	 protected $fillable = ['email', 'password', 'role_id', 'org_id', 'status', 'created_at'];

}

