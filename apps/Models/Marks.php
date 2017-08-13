<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Marks extends Eloquent
{
	protected $table = 'marks';
	protected $fillable = ['exam_id', 'user_id', 'mark'];
}