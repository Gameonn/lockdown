<?php session_start();?>
<?php require_once "../php_include/db_connection.php";

require_once("DataClass.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_lockdown');
$db->show_errors();
require_once('../twilio/Services/Twilio.php'); 
$key=$_SESSION['school']['token'];
$sth=$conn->prepare("select * from school join school_admin on school_admin.school_id=school.id where `token`=:access_token");
$sth->bindValue('access_token',$key);
try{$sth->execute();}
catch(Exception $e){}
$result=$sth->fetchAll(PDO::FETCH_ASSOC);
$sid=$result[0]['school_id'];

$sql="update lockdown set user_id2=:uid,status=:status where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('status',$status);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('id',$lockdown_id);
	try{$sth->execute();
	$success=1;
	//$msg="Lockdown status updated";
	}
	catch(Exception $e){
	echo $e->getMessage();
	}

	
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
	    	
	    			
	    			if(!empty($apn_ids)){
	    			
	    				$apns->newMessage($apn_ids);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('l', $lid);
					$apns->addMessageCustom('s', $message['status']);
					$apns->queueMessage();
					$apns->processQueue();
	    			}
	    			else{
	    			}
	    			
	    		
	    		if(!empty($reg_ids))
	    		{	
	    		
	    			GCM::send_notification($reg_ids, $message);
	    		}
?>