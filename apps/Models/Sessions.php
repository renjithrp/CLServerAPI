<?php

namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Sessions extends Eloquent
{
	#protected $table = 'cldb_Sessions';
	protected $fillable = ['token', 'user_id', 'created_at', 'valid_till', 'status', 'updated_at'];

}
