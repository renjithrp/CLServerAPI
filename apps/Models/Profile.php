<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Profile extends Eloquent
{
	protected $table = 'profile';

	 protected $fillable = ['firstname','lastname','address','skills', 'phone', 'web', 'about', 'dp', 'org_id','user_id','role_id','status'];

}