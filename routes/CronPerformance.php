<?php
use Apps\Models\Sections;
use Apps\Models\Subject;
use Apps\Models\Exams;
use Apps\Models\Marks;
use Apps\Models\Qustions;
use Apps\Models\Answers;
use Apps\Models\Performance;
use Apps\Controllers\Messages as m;
use Apps\Controllers\Getid;
use Apps\Controllers\GetName;


function CronPerformance($request, $response, $args) {

 $Marks = Marks::select('exam_id','user_id','mark')
 		->where('user_id','373')
 		->get();
 $m = new m;
 $s = $Marks->average('mark');
 return $m->data($response,$s);

}