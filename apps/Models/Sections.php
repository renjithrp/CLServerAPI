<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Sections extends Eloquent
{
	protected $table = 'section';
	protected $fillable = ['name', 'description', 'user_id', 'org_id','status'];

}
