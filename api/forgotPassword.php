<?php
//this is an api to recover password
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once "../php_include/db_connection.php"; 
require_once('../PHPMailer_5.2.4/class.phpmailer.php');


function sendEmail($email,$subjectMail,$bodyMail,$email_back){

	$mail = new PHPMailer(true); 
	$mail->IsSMTP(); // telling the class to use SMTP
	try {
	  //$mail->Host       = SMTP_HOST; // SMTP server
	  $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	  $mail->SMTPAuth   = true;                  // enable SMTP authentication
	  $mail->Host       = SMTP_HOST; // sets the SMTP server
	  $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
	  $mail->Username   = SMTP_USER; // SMTP account username
	  $mail->Password   = SMTP_PASSWORD;        // SMTP account password
	  $mail->AddAddress($email, '');     // SMTP account password
	  $mail->SetFrom(SMTP_EMAIL, SMTP_NAME);
	  $mail->AddReplyTo($email_back, SMTP_NAME);
	  $mail->Subject = $subjectMail;
	  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automaticall//y
	  $mail->MsgHTML($bodyMail) ;
	  if(!$mail->Send()){
			$success='0';
			$msg="Error in sending mail";
	  }else{
			$success='1';
	  }
	} catch (phpmailerException $e) {
	  $msg=$e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $msg=$e->getMessage(); //Boring error messages from anything else!
	}
	//echo $msg;
}




$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$email=$_REQUEST['email'];

if(!($email)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sql="select * from users where email=:email ";
	$sth=$conn->prepare($sql);
	$sth->bindValue("email",$email);
	try{$sth->execute();}catch(Exception $e){
	echo $e->getMessage();
	}
	$res=$sth->fetchAll();

	if(count($res)){
	$token=$res[0]['access_token'];
	$user_type=$res[0]['user_type'];
	
		$success="1";
		$msg="Mail sent";
		
		$user_type=ucwords($user_type);
		sendEmail($email,"Lockdown- Recover Password",
						"<div style='font-size:20px;line-height:1.6;'>
							<p>Dear $user_type,</p>
							<br>
							<p>we have received your password reset request.</p>
							<p>Please follow the link below to set a new password:</p>
							<p><a href='".BASE_PATH."reset-password.php?token={$token}'>Reset URL</a></p>
							<p>In case you have any questions or have not requested to change your password, please reach out to admin@lockdown.com.</p>
							<br>
							<p>Best,</p>
							<p>Lockdown Users</p>
						</div>"
					,SMTP_EMAIL);
	
}
else{
		$success="0";
		$msg="No account with that email address exist";
	}
	}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>