<?php session_start();?>
<?php require_once "../php_include/db_connection.php";
//require_once("../api/DataClass.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_lockdown');
//$db->show_errors();
require_once('../twilio/Services/Twilio.php');
 $success=$msg=0;
 
 
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
	  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';  // optional - MsgHTML will create an alternate automaticall//y
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
 
	function sendSMS($people,$body){
    $AccountSid = "AC1c5e088e9bd46072f6b53dba567f372b";
    $AuthToken = "355870cfd698d03fc3f62143cb081558";

	// Instantiate a new Twilio Rest Client
	$client = new Services_Twilio($AccountSid, $AuthToken);

	/* Your Twilio Number or Outgoing Caller ID */
	$from = '+1 415-658-9055';

	foreach ($people as $to => $name) {
		// Send a new outgoing SMS */
		
		$client->account->sms_messages->create($from, $to, $body);
		//echo "Sent message to $name";
		//echo $to." ". $body;
		}

		}
 
	function sendcall($people,$sid){

	global $conn;
	  require '../twilio/Services/Twilio.php';
	$sql="select * from school where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$sid);
	try{$sth->execute();}
	catch(Exception $e){}
	
	$res=$sth->fetchAll();
	$address=$res[0]['address'];
	$city=$res[0]['city'];
	$school_name=$res[0]['school_name'];
	
	$myfile = fopen("voice1.xml", "w") or die("Unable to open file!");
	$txt = '<?xml version="1.0" encoding="UTF-8" ?> ';
	fwrite($myfile, $txt);
	$txt = " <Response> ";
	fwrite($myfile, $txt);
	$txt=" <Say>Hello Police</Say> ";
	fwrite($myfile, $txt);
	$txt=' <Say voice="alice" language="en-GB" loop="2">There is a lockdown in our school '. $school_name.'  </Say> ';
	fwrite($myfile, $txt);
	$txt=' <Say voice="alice" language="en-GB" loop="2"> Our addess is'. $address .' and city '. $city .' </Say>  ';
	fwrite($myfile, $txt);
	$txt = " </Response> ";
	fwrite($myfile, $txt);
	fclose($myfile);
	
  $sid= "AC1c5e088e9bd46072f6b53dba567f372b";
    $token= "355870cfd698d03fc3f62143cb081558";
	
	/* Your Twilio Number or Outgoing Caller ID */
	    $version = "2010-04-01";
  	$from = '+1 415-658-9055';
	//$to= '+91 9653783614';
   $client = new Services_Twilio($sid, $token, $version);
   
   
	foreach ($people as $to) {
	
	$call = $client->account->calls->create(
            $from, // The number of the phone initiating the call
            $to,  // The number of the phone receiving call
            'http://www.code-brew.com/projects/lockdown/school/voice1.xml' // The URL Twilio will request when the call is answered
        );
		
	}
	return true;
	}

	$key=$_SESSION['school']['token'];
	$sth=$conn->prepare("select * from school join school_admin on school_admin.school_id=school.id where `token`=:access_token");
	$sth->bindValue('access_token',$key);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$sid=$result[0]['school_id'];
	$school_id=$sid;
	$base=BASE_PATH;
	$add="school";
	$redirect="/lockdown.php";
	//$lid=$_REQUEST['lockdown_id'];
	$status=$_REQUEST['status'];
	
	$sql="select * from lockdown where school_id=:sid order by lockdown.created_on DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue('sid',$sid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res1=$sth->fetchAll();
	$state=$res1[0]['status'];
	$lid=$res1[0]['id'];
	if($state==1) $status=2;
	elseif($state==2) $status=4;
	else $status=2;
	if($state==2){
	$sql="update lockdown set user_id2=0,status=4 where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$lid);
	try{$sth->execute();}
	catch(Exception $e){}
	
	}
	elseif($state==1){
	$sql="update lockdown set user_id2=0,status=2 where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$lid);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	
	else{
	$sql="Insert into lockdown values(DEFAULT,0,0,:status,:sid,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('sid',$sid);
	$sth->bindValue('status',$status);
	try{$sth->execute();
	$success=1;
	//$msg="Lockdown status updated";
	}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	}
	if($status==2){
	$msg= "Lockdown Activated by School Admin";
	
	$sql="select * from school_admin where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$sid);
	try{ $sth->execute();}
	catch(Exception $e){}
	$ad_det=$sth->fetchAll();
	
	foreach($ad_det as $row){
	sendEmail($row['principal_email'],$msg,$msg,SMTP_EMAIL);
	$cellphone1[]= $row['principal_phone'];
	    $names1[]= 'School Admin';  
	}
	/*$people1=array(
	$cellphone1 => 'School Admin';
	)*/
	
	$people1 = array_combine($cellphone1, $names1);
	
	$body1="Lockdown Activated by School Admin";
	try{
	sendSMS($people1,$body1);
	}
	catch(Exception $e){}
	
	
	$sql="select * from users where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$sid);
	try{ $sth->execute();}
	catch(Exception $e){}
	$user_det=$sth->fetchAll();
	foreach($user_det as $row){
	sendEmail($row['email'],$msg,$msg,SMTP_EMAIL);
	
	 if(!empty($row['reg_id'])){
	    		$reg_ids[]=$row['reg_id'];		
	    }
	    	
	 if(!empty($row['apn_id'])){
	    	$apn_ids[]=$row['apn_id'];			
	    }
	    $cellphone[]= $row['country_code']. $row['cellphone'];
	    $names[]= $row['name'];  
		
	   }
	
	
	$people = array_combine($cellphone, $names);
	
	$body="Lockdown Activated by School Admin";
	try{
	sendSMS($people,$body);
	}
	catch(Exception $e){}
	//For GCM..................
			$message=array();
	    		
	    		$message['lockdown_id']=$lid;
	    		$message['msg']=$msg;
	    		$message['status']=2;
	    		//$message['uid']=$uid;
	    	
	    			
	    			if(!empty($apn_ids)){
	    				$apns->newMessage($apn_ids);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    			}
	    			else{}
	    			
	    		
	    		if(!empty($reg_ids))
	    		{	
					GCM::send_notification($reg_ids, $message);
	    		}
			}

		elseif($status==4){
		$success=1;
		$msg= "School is now safe";
		
		
	$sql="select * from school_admin where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$sid);
	try{ $sth->execute();}
	catch(Exception $e){}
	$ad_det=$sth->fetchAll();
		foreach($ad_det as $row){
	sendEmail($row['principal_email'],$msg,$msg,SMTP_EMAIL);
	$cellphone1[]= $row['principal_phone'];
	    $names1[]= 'School Admin';  
	}
	
	$people1 = array_combine($cellphone1, $names1);
	$body1="Lockdown Activated by School Admin";
	try{
	sendSMS($people1,$body1);
	}
	catch(Exception $e){}	
		
	$sql="select * from users where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$school_id);
	try{ $sth->execute();}catch(Exception $e){}
	$user_det=$sth->fetchAll();
	foreach($user_det as $row){
	sendEmail($row['email'],$msg,$msg,SMTP_EMAIL);
	
	 if(!empty($row['reg_id'])){
	    				$reg_ids[]=$row['reg_id'];
	    				
	    	}
	    	
	    if(!empty($row['apn_id'])){
	    				$apn_ids[]=$row['apn_id'];
	    				
	    	}
	    	
	    	   $cellphone[]= $row['country_code']. $row['cellphone'];
	     $names[]= $row['name']; 
	}
	
	$people = array_combine($cellphone, $names);
	
	$body="School is now safe";
	try{
	sendSMS($people,$body);
	}
	catch(Exception $e){}
	
	//For GCM..................
			$message=array();
	    		
	    		$message['lockdown_id']=$lid;
	    		$message['msg']=$msg;
	    		$message['status']=4;
	    		//$message['uid']=($uid)?$uid:0;
	    	
	    			
	    			if(!empty($apn_ids)){
	    			
	    			$apns->newMessage($apn_ids);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    			}
	    			else{}
	    		if(!empty($reg_ids)){	
					GCM::send_notification($reg_ids, $message);
	    		}
			
			}	
	header("Location: $base$add$redirect?success=$success&msg=$msg");
         // header("Location: $base $add $redirect");
?>