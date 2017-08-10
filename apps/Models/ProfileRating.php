<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class ProfileRating extends Eloquent
{
	protected $table = 'profilerating';

	 protected $fillable = ['rating','pro_id','user_id'];

}