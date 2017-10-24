<?php
namespace Apps\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Performance extends Eloquent
{
	protected $table = 'performance';
	protected $fillable = ['profile_id', 'performance', 'org_id'];

}
