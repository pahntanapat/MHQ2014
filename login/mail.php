<?php
require 'phpmailer/PHPMailerAutoload.php';
require_once 'config.inc.php';
function sendMail($to,$subject,$message,$isSMTP=NULL){
	try{
		$mail=new PHPMailer(true);
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = "quoted-printable";

		if($isSMTP===true){
			$mail->isSMTP();
			//for debugging only!
#			$mail->SMTPDebug = 2;
#			$mail->Debugoutput = 'html';
			$mail->SMTPAuth = true;
			$mail->host=SMTP_HOST;
			$mail->Port = SMTP_PORT; // 25 for others 578 465
#			$mail->SMTPSecure = 'ssl'; //unset for others
			$mail->Username = SMTP_USER;
			$mail->Password = SMTP_PASS;
		}else
			$mail->isMail();

		$mail->setFrom("no-reply@test.net");
		$mail->addReplyTo("info@test.net");
		$mail->addAddress($to);

		$mail->Subject = $subject.': Mahidol Quiz';
		$msg=<<<HTM
<h1>$subject</h1><br/>
<h2>Mahidol Quiz</h2><hr/><br/>
<p>$message</p>
<br/><hr/>
<p>Automatic message from: {$_SERVER['SERVER_NAME']} Please reply to {$mail->Username}</p>
HTM;
		$mail->msgHTML($msg, dirname(__FILE__),false);

		return $mail->send();
	}catch(phpmailerException $e) {
   		return $e->errorMessage();
	} catch (Exception $e) {
    	return $e->getMessage();
	}
}

function forceSendMail($to,$subject,$message){
	$msg=sendMail($to,$subject,$message,true);
	if($msg!==true){
		$m=sendMail($to,$subject,$message,false);
		if($m!==true) return $msg."<br/>\n".$m;
	}
	return true;
}
?>