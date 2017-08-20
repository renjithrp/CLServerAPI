<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Profiledp extends Eloquent
{
	protected $table = 'profiledp';

	 protected $fillable = ['dp','profile_id','status'];

}