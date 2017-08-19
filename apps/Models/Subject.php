<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Subject extends Eloquent
{
	protected $table = 'subject';
	protected $fillable = ['name', 'description', 'user_id', 'sec_id', 'status'];

}