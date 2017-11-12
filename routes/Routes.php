<?php

//userRoutes//
$app->get('/login','UserLogin');
$app->get('/logout','UserLogout');
$app->post('/signup','UserSignup');


#ProfileRoutes
$app->get('/profile', 'GetProfile');
$app->put('/profile', 'UpdateProfile');
$app->post('/profile', 'CreateProfile');


//Testimonials

$app->get('/testimonials', 'GetTestimonials');
$app->get('/testimonials/{pro_id}', 'GetProfleTestimonial');
$app->post('/testimonials/{pro_id}', 'CreateProfileTestimonial');
$app->put('/testimonials/{pro_id}/{id}', 'UpdateProfileTestimonial');

//Rating
$app->get('/rating','GetRating');
$app->get('/rating/{pro_id}', 'GetUserRating');
$app->post('/rating/{pro_id}', 'PostRating');

//section Routes//
$app->get('/sections', 'GetSections');
$app->get('/sections/{sec_id}', 'GetSubjects');
$app->get('/sections/{sec_id}/subjects', 'GetSubjects');
$app->get('/sections/{sec_id}/subjects/{subj_id}', 'GetNotesAndExams');
$app->get('/sections/{sec_id}/subjects/{subj_id}/notes', 'GetAllNotes');
$app->get('/sections/{sec_id}/subjects/{subj_id}/exams', 'GetAllExams');
$app->get('/sections/{sec_id}/subjects/{subj_id}/notes/{note_id}', 'GetNote');
$app->get('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}', 'GetExam');


//Create Section
$app->post('/sections', 'CreateSections');
$app->put('/sections/{sec_id}', 'UpdateSections');

//create Subject
$app->post('/sections/{sec_id}', 'CreateSubjects');
$app->post('/sections/{sec_id}/subjects', 'CreateSubjects');
$app->put('/sections/{sec_id}/subjects/{subj_id}', 'UpdateSubjects');

//link section and subject
$app->post('/linkprofile', 'UpdateLinkProfile');
$app->get('/linkprofile', 'GetLinkProfile');

//Exams Routes
$app->get('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}/attend', 'AttendExam');
$app->post('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}/attend', 'PostExam');
$app->post('/sections/{sec_id}/subjects/{subj_id}/exams', 'CreateExam');
$app->put('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}', 'UpdateExam');
$app->post('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}/qa', 'CreateQustions');



//Notes

$app->post('/sections/{sec_id}/subjects/{subj_id}/notes', 'CreateNotes');
$app->put('/sections/{sec_id}/subjects/{subj_id}/notes/{note_id}', 'UpdateNotes');

//Role
$app->get('/role', 'GetRole');


//VeryfyEmail
$app->post('/verify', 'VeryfyEmail');


//Dp
$app->post('/dp', 'Uploaddp');
$app->get('/dp', 'Getdp');

//Search
$app->get('/search/{query}', 'Search');

//Performance
$app->get('/cronp', 'CronPerformance');
$app->get('/performance', 'Performance');
$app->get('/performance/{pro_id}', 'ProPerformance');


//Marks
$app->get('/marks/{pro_id}', 'GetMarks');