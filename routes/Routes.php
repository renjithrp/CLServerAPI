<?php

//userRoutes//
$app->get('/login','UserLogin');
$app->get('/logout','UserLogout');
$app->post('/signup','UserSignup');

//Testimonials

$app->get('/testimonials', 'GetTestimonials');
$app->get('/testimonials/{pro_id}', 'GetProfleTestimonial');
$app->post('/testimonials/{pro_id}', 'CreateProfileTestimonial');
$app->put('/testimonials/{pro_id}/{id}', 'UpdateProfileTestimonial');

//section Routes//
$app->get('/sections', 'GetSections');
$app->get('/sections/{sec_id}', 'GetSubjects');
$app->get('/sections/{sec_id}/subjects', 'GetSubjects');
$app->get('/sections/{sec_id}/subjects/{subj_id}', 'GetNotesAndExams');
$app->get('/sections/{sec_id}/subjects/{subj_id}/notes', 'GetAllNotes');
$app->get('/sections/{sec_id}/subjects/{subj_id}/exams', 'GetAllExams');
$app->get('/sections/{sec_id}/subjects/{subj_id}/notes/{note_id}', 'GetNote');
$app->get('/sections/{sec_id}/subjects/{subj_id}/exams/{exam_id}', 'GetExam');