<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Answers extends Eloquent
{
	protected $table = 'answers';
	protected $fillable = ['answer', 'qust_id', 'flag','status', 'created_at'];

}
