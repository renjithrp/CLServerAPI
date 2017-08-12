<?php

namespace Apps\Controllers;

use PHPMailer;

class SendEmail {

	function verification($email,$pin){
    
    	$mail = new PHPMailer;

    	$mail->setFrom('renjith.net@hotmail.com', 'CollobarateLearning');
    	$mail->addAddress($email);
    	$mail->addReplyTo('no-reply@CollobarateLearning.com', 'CollobarateLearning.com');

    	$mail->isHTML(true);                                  // Set email format to HTML

    	$mail->Subject = "Verification code $pin";
    	$mail->Body    = "Verification code $pin";

    	if($mail->send()) {

    		return True;
       
    	}  
	}
}