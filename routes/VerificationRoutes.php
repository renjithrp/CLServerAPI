<?php

function VeryfyEmail($request, $response, $args){

	$i = 0; //counter
    $pin = ""; //our default pin is blank.
    while($i < 4){
        //generate a random number between 0 and 9.
        $pin .= mt_rand(0, 9);
        $i++;
    }
    
    $mail = new PHPMailer;

    $mail->setFrom('renjith.net@hotmail.com', 'CollobarateLearning');
    $mail->addAddress($request->getParam('email'));
    $mail->addReplyTo('no-reply@CollobarateLearning.com', 'CollobarateLearning.com');

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = "Verification code $pin";
    $mail->Body    = "Verification code $pin";

    if(!$mail->send()) {
       echo "sss";
    }   
  }  