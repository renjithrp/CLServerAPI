<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Qustions extends Eloquent
{
	protected $table = 'qustion';
	protected $fillable = ['question', 'exam_id', 'status', 'created_at'];

}
