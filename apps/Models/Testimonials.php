<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Testimonials extends Eloquent
{
	protected $table = 'testimonials';

	 protected $fillable = ['comments','pro_id','user_id','status'];

}