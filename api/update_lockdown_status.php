<?php
//this is an api to request lockdown
//Lockdown status 1-pending, 2- approved, 3- rejected, 4- safe
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_lockdown');
//$db->show_errors();
require_once('../twilio/Services/Twilio.php');
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$access_token=$_REQUEST['access_token'];
$lockdown_id=$_REQUEST['lockdown_id']; 
$lid=$lockdown_id;
$status=$_REQUEST['status'];
global $conn;

if(!($access_token && $lockdown_id && $lockdown_id!='null' && $lockdown_id!='nil' && $status)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{ 
	$sql="select users.id as uid,users.school_id,users.name,users.position,users.room,users.country_code,users.cellphone,users.landline,users.user_type,users.email,school.authority_numbers from users join school on school.id=users.school_id where users.access_token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("token",$access_token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uid=$result[0]['uid'];
	$utype=$result[0]['user_type'];
	$room=$result[0]['room'];
	$uname=$result[0]['name'];
	$school_id=$result[0]['school_id'];
	
	if(count($result)){
	if($utype=='warden'){
	$sql="update lockdown set user_id2=:uid,status=:status where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('status',$status);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('id',$lockdown_id);
	
	try{$sth->execute();
	$success=1;
	}
	catch(Exception $e){}
	
	
	if($status==2){
	
	$msg= "Activated by Warden: \n ".$uname;
	
	$msg1="Lockdown Activated By Warden ".$uname;
	
	//email and sms to administrator
	/*$sql="select * from school_admin where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$school_id);
	try{ $sth->execute();}
	catch(Exception $e){}
	$ad_det=$sth->fetchAll();
		foreach($ad_det as $row){
	DataClass::sendEmail($row['principal_email'],$msg,$msg,SMTP_EMAIL);
	$cellphone1[]= $row['principal_phone'];
	    $names1[]= 'School Admin';  
	}
	
	$people1 = array_combine($cellphone1, $names1);
	$body1="Lockdown Activated By Warden ".$uname;
	try{
	DataClass::sendSMS($people1,$body1);
	}
	catch(Exception $e){}	
	*/
	
	
	$sql="select * from users where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$school_id);
	try{ $sth->execute();}catch(Exception $e){}
	$user_det=$sth->fetchAll();
	foreach($user_det as $row){
	DataClass::sendEmail($row['email'],$msg,$msg,SMTP_EMAIL);
	
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
	
	$body="Activated by Warden ".$uname;
	try{
	DataClass::sendSMS($people,$body);
	}
	catch(Exception $e){}
	//For GCM..................
			$message=array();
	    		
	    		$message['lockdown_id']=$lid;
	    		$message['msg']=$msg;
	    		$message['status']=2;
	    		$message['uid']=$uid;
	    	
	    			
	    			foreach($user_det as $row){
					if(!empty($row['apn_id'])){
					try{
	    				$apns->newMessage($row['apn_id']);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    				}
	    				catch(Exception $e){}
	    				
	    			}
	    			}
	    			
	    		
	    		if(!empty($reg_ids))
	    		{	
	    		
	    			GCM::send_notification($reg_ids, $message);
	    		}
	
	}

	elseif($status==4){
	
	 $sql="update `lockdown` set status=4 where  Date_format(created_on,'%Y-%m-%d')= Date_format(NOW(),'%Y-%m-%d') and id<=:lid and school_id=:sid";
	  $sth=$conn->prepare($sql);
	  $sth->bindValue('lid',$lid);
	  $sth->bindValue('sid',$school_id);
	  try{$sth->execute();}
	  catch(Exception $e){}
	  
	  
	 //email and sms to administrator 
	 
	$msg1="School is now in Safe Mode";
	/*$sql="select * from school_admin where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$school_id);
	try{ $sth->execute();}
	catch(Exception $e){}
	$ad_det=$sth->fetchAll();
	foreach($ad_det as $row){
	DataClass::sendEmail($row['principal_email'],$msg,$msg,SMTP_EMAIL);
	$cellphone1[]= $row['principal_phone'];
	    $names1[]= 'School Admin';  
	}
	
	$people1 = array_combine($cellphone1, $names1);
	$body1="School is now in Safe Mode";
	try{
	DataClass::sendSMS($people1,$body1);
	}
	catch(Exception $e){}	 
	*/
	
	$msg= "School is now safe";
	$sql="select * from users where school_id=:school_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('school_id',$school_id);
	try{ $sth->execute();}catch(Exception $e){}
	$user_det=$sth->fetchAll();
	foreach($user_det as $row){
	DataClass::sendEmail($row['email'],$msg,$msg,SMTP_EMAIL);
	
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
	DataClass::sendSMS($people,$body);
	}
	catch(Exception $e){}
	
	//For GCM..................
			$message=array();
	    		
	    		$message['lockdown_id']=$lid;
	    		$message['msg']=$msg;
	    		$message['status']=4;
	    		$message['uid']=$uid;
	    	
	    			
	    			foreach($user_det as $row){
					if(!empty($row['apn_id'])){
					try{
	    				$apns->newMessage($row['apn_id']);
					$apns->addMessageAlert($message['msg']);
					//$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    				}
	    				catch(Exception $e){}
	    			
	    			}
	    			}
	    			
	    		
	    		if(!empty($reg_ids))
	    		{	
	    		
	    			GCM::send_notification($reg_ids, $message);
	    		}
	}
	}
	else{
	$success=0;
	$msg="Access denied to staff";
	}
	
	
	}
	else{
	$success=0;
	$msg="Invalid access token";
	}
	
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1)
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$result));

else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>