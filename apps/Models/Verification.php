<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Verification extends Eloquent
{
	protected $table = 'verification';
	protected $fillable = ['email', 'code', 'status'];
}