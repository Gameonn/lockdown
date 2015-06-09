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

require_once('../twilio/Services/Twilio.php');

$success=$msg="0";$data=array();$reg_ids=array();$apn_ids=array();$people=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$access_token=$_REQUEST['access_token']; 
$path=BASE_PATH.'/api';
global $conn;

if(!($access_token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

else{ 

	$sql="select users.*,users.id as uid,school.* from users join school on school.id=users.school_id where users.access_token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("token",$access_token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uname=$result[0]['name'];
	$uid=$result[0]['uid'];
	$utype=$result[0]['user_type'];
	$room=$result[0]['room'];
	$school_id=$result[0]['school_id'];
	
	if(count($result)){
	
	$sql="select lockdown.* from lockdown where school_id=:school_id order by lockdown.id DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue("school_id",$school_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	//if(count($res)){
	$l_status=$res[0]['status'];
	if($l_status!=2){
	if($utype=='staff'){
	$sql="Insert into lockdown values(DEFAULT,:user_id,0,1,:school_id,NOW())";
	}
	else{
	 $sql="Insert into lockdown values(DEFAULT,0,:user_id,2,:school_id,NOW())";
	}
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	$sth->bindValue('school_id',$school_id);
	try{$sth->execute();
	$lid=$conn->lastInsertId();
	}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$success=1;
		if($utype=='staff'){
		$msg="by ".$uname;
		
		$msg1="Lockdown Requested By ".$uname;
		
		
		//email and sms to administrator
		/*$sql="select * from school_admin where school_id=:school_id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('school_id',$school_id);
		try{$sth->execute();}
		catch(Exception $e){}
		$ad_det=$sth->fetchAll();
		if(is_array($ad_det) || is_object($ad_det)){
		foreach($ad_det as $k=>$v){
		
		    DataClass::sendEmail($v['principal_email'],$msg,$msg,SMTP_EMAIL);
		    $cellphone1[]= $v['principal_phone'];
		    $names1[]= 'School Admin';  
		}
		
		$people1 = array_combine($cellphone1, $names1);
		$body1="Lockdown Requested By ".$uname;
		try{
		DataClass::sendSMS($people1,$body1);
		}
		catch(Exception $e){}	
		}*/
			
		$sql="select * from users where school_id=:school_id and user_type='warden'";
		$sth=$conn->prepare($sql);
		$sth->bindValue('school_id',$school_id);
		try{ $sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		$user_det=$sth->fetchAll();
		if($user_det){
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
		}
		$body="Lockdown requested by ".$uname;
		try{
		DataClass::sendSMS($people,$body);
		}
		catch(Exception $e){}
		//For GCM..................
				$message=array();
					$message['lockdown_id']=$lid;
					$message['msg']=$msg;
					$message['status']=1;
					$message['room']=$room;
					$message['uid']=$uid;
					
					
					if(!empty($reg_ids)){
					GCM::send_notification($reg_ids, $message);
					}
					
					
					foreach($user_det as $row){
					if(!empty($row['apn_id'])){
						try{
						$apns->newMessage($row['apn_id']);
						$apns->addMessageAlert($message['msg']);
						$apns->addMessageSound('Siren.mp3');
						$apns->addMessageCustom('l', $lid);
						$apns->addMessageCustom('r', $message['room']);
						$apns->addMessageCustom('s', $message['status']);
						$apns->queueMessage();
						$apns->processQueue();
						}
						catch(Exception $e){}
						
						}
						}
						
					
		}// if ends for staff push
	else{
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
	try{ $sth->execute();}
	catch(Exception $e){}
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
	    		$message['room']=$room;
	    		$message['uid']=$uid;
	    	
	    		if(!empty($reg_ids)){	
	    		GCM::send_notification($reg_ids, $message);
	    			}
	    			
	    			foreach($user_det as $row){
					if(!empty($row['apn_id'])){
					try{
	    				$apns->newMessage($row['apn_id']);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('r', $message['room']);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    				}
	    				catch(Exception $e){}
	    			
	    			}
	    			}		
	}// else ends for warden push
	
				foreach($result as $key=>$value){
				
					$data[]=array(
					    "uid"=> $value['uid'],
				            "name"=> $value['name']?$value['name']:"",
				            "position"=> $value['position']?$value['position']:"",
				            "room"=> $value['room']?$value['room']:"",
				            "phone"=> $value['cellphone']?$value['cellphone']:"",
				            "landline"=> $value['landline']?$value['landline']:"",
				            "email"=> $value['email'],
				            "access_token"=> $value['access_token'],
				            "user_type"=> $value['user_type'],
				            "lockdown_id"=> $lid,				            
				            "authority_number"=> $value['authority_numbers']?$value['authority_numbers']:""
					);
				}
	}// if ends for status!=2
	else{
	$success=0;
	$msg="School in Lockdown already";
	}
	//}
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
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
else
echo json_encode(array("success"=>$success,"msg"=>$msg));

?>