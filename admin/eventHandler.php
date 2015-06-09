<?php 
//this page is to handle all the admin events occured at client side
 require_once("../php_include/db_connection.php"); 
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_lockdown');
require_once('../twilio/Services/Twilio.php');
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}

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
      
    function generateRandomString($length = 6){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
	  
	$success=0;
	$msg="";
	session_start();
	//switch case to handle different events
	switch($_REQUEST['event']){
	case "signin":   
	//print_r($_REQUEST);die;  
		$success=0;
		$user=$_REQUEST['email'];
		$password=$_REQUEST['password'];
		$redirect=$_REQUEST['redirect'];
		$sth=$conn->prepare("select * from admin where email=:email");
		$sth->bindValue("email",$user);
		try{$sth->execute();}catch(Exception $e){
		echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		//print_r($result);die;
		if(count($result)){
			foreach($result as $row){
		
				if($row['password']==md5($password)){
					session_start();
					$success=1;
					
					$_SESSION['admin']['id']=$row['id'];
					$_SESSION['admin']['email']=$row['email'];
					
				}
			}
		}
		if(!$success){
			$redirect="index.php";
			$msg="Invalid Username/Password";
		}
		header("Location: $redirect?success=$success&msg=$msg");
		break;
	
	
	   case "add-school":  
	   
          $success=0;
          $s_name=$_REQUEST['school_name'];
          $signup_date=$_REQUEST['signup_date']?$_REQUEST['signup_date']:date('Y-m-d H:i:s');
          $address=$_REQUEST['address']?$_REQUEST['address']:"";
          $city=$_REQUEST['city']?$_REQUEST['city']:"";
          $redirect=$_REQUEST['redirect'];
		  $password=generateRandomString();
		  $zipcode=$_REQUEST['zipcode']?$_REQUEST['zipcode']:"";
		  $country=$_REQUEST['country']?$_REQUEST['country']:"";
		  $pr_email=$_REQUEST['pr_email'] ? $_REQUEST['pr_email']: "";
		  $country_code=$_REQUEST['country_code'] ? $_REQUEST['country_code']: "";
          $pr_phone=$_REQUEST['pr_phone'] ? $_REQUEST['pr_phone']: "";
          $pr_phone=$country_code. $pr_phone;
          $code=md5($pr_email. rand(1,9999999));
          $path=BASE_PATH.'school';	
		  $msg1="Signin your Account";
		  $msg2="You are registered to the lockdown system with following credentials<br>
						<div style='font-size:16px;line-height:1.4;'>
							<p>Dear Administrator,</p>
							<p>we have sent your login credentials along with your concerned school.</p>
							<p>Please use following credentials to login:</p>
							<p><a href='$path'>$path</a></p>
							<p>Email=> $pr_email</p>
							<p>School Name=> $s_name </p>
							<p>Password=> $password </p>
							
							<br>
							<p>Best,</p>
							<p>Lockdown Users</p>
						</div>  ";
		
          $sth=$conn->prepare('Insert into school(id,school_name,school_logo,address,city,zipcode,country,authority_numbers,status,created_on,updated_on) values(DEFAULT,:school_name,"",:address,:city,:zipcode,:country,"",1,:signup_date,NOW())');
          $sth->bindValue("school_name",$s_name);
          $sth->bindValue("address",$address);
          $sth->bindValue("city",$city);
          $sth->bindValue("zipcode",$zipcode);
           $sth->bindValue("signup_date",$signup_date);
          $sth->bindValue("country",$country);
          $count=0;
          try{$count=$sth->execute();
		  $sid=$conn->lastInsertId();
		  $success=1;
		  $msg="School Added";
		  
	sendEmail($pr_email,$msg1,$msg2,SMTP_EMAIL);
	/*if($pr_phone){
	$people=array(
	$pr_phone=>$pr_email
	);
	}
	$body="Lockdown login credentials:
	Email: $pr_email, Password: $password";
	if($people){
	try{
	sendSMS($people,$body);
          }
          catch(Exception $e){}
          }*/
          }
          catch(Exception $e){
            echo $e->getMessage();
          }
		  
	 $sth=$conn->prepare('Insert into school_admin(id,school_id,principal_email,password,principal_phone,token,status,created_on) values(DEFAULT,:school_id,:pr_email,:password,:pr_phone,:key,0,NOW())');
          $sth->bindValue("school_id",$sid);
		$sth->bindValue("key",md5($code));
          $sth->bindValue("pr_email",$pr_email);
		$sth->bindValue("password",md5($password));
          $sth->bindValue("pr_phone",$pr_phone);
          $count=0;
          try{$count=$sth->execute();}
		  catch(Exception $e){}
		 	  	  
   
          header("Location: $redirect?success=$success&msg=$msg");
          break;
		  
		case "add-school-admin": 
	   //print_r($_REQUEST);die;
          $success=0;
          $sid=$_REQUEST['school_id'];
          $redirect=$_REQUEST['redirect'];
		  $password=generateRandomString();
		  $pr_email=$_REQUEST['pr_email'] ? $_REQUEST['pr_email']: "";
		  $country_code=$_REQUEST['country_code'] ? $_REQUEST['country_code']: "";
          $pr_phone=$_REQUEST['pr_phone'] ? $_REQUEST['pr_phone']: "";
          $pr_phone=$country_code. $pr_phone;
          $code=md5($pr_email. rand(1,9999999));
          $path=BASE_PATH.'school';	
		  $sql="select * from school where id=:id";
		  $sth=$conn->prepare($sql);
		  $sth->bindValue('id',sid);
		  try{$sth->execute();}
		  catch(Exception $e){}
		  $res3=$sth->fetchAll();
		  $s_name=$res3[0]['school_name'];
		  
		  $msg1="Signin your Account";
		  $msg2="You are registered to the lockdown system with following credentials<br>
						<div style='font-size:16px;line-height:1.4;'>
							<p>Dear Administrator,</p>
							<p>we have sent your login credentials along with your concerned school.</p>
							<p>Please use following credentials to login:</p>
							<p><a href='$path'>$path</a></p>
							<p>Email=> $pr_email</p>
							<p>School Name=> $s_name </p>
							<p>Password=> $password </p>
							<br>
							<p>Best,</p>
							<p>Lockdown Users</p>
						</div>  ";
		
       	 $sth=$conn->prepare('Insert into school_admin(id,school_id,principal_email,password,principal_phone,token,status,created_on) values(DEFAULT,:school_id,:pr_email,:password,:pr_phone,:key,0,NOW())');
          $sth->bindValue("school_id",$sid);
	  $sth->bindValue("key",md5($code));
          $sth->bindValue("pr_email",$pr_email);
	  $sth->bindValue("password",md5($password));
          $sth->bindValue("pr_phone",$pr_phone);
          $count=0;
         try{$count=$sth->execute();
		  $success=1;
		  $msg="School Admin Added";
		  
	sendEmail($pr_email,$msg1,$msg2,SMTP_EMAIL);
	/*if($pr_phone){
	$people=array(
	$pr_phone=>$pr_email
	);
	}
	$body="Lockdown login credentials
	Email: $pr_email, Password: $password";
	if($people){
	try{
	sendSMS($people,$body);
          }
          catch(Exception $e){}
          }*/
          }
          catch(Exception $e){
            echo $e->getMessage();
          }
   header("Location: $redirect?success=$success&msg=$msg");
          break;
          
        case "edit-school": 
        
	$i=sizeof($_REQUEST['pr_id']);
				
          $success=0;
          $id=$_REQUEST['school_id'];
          $signup_date=$_REQUEST['signup_date'];
          $s_name=$_REQUEST['school_name'];
          $address=$_REQUEST['address']?$_REQUEST['address']:"";
          $city=$_REQUEST['city']?$_REQUEST['city']:"";
           $zipcode=$_REQUEST['zipcode']?$_REQUEST['zipcode']:"";
           $country=$_REQUEST['country']?$_REQUEST['country']:"";
            $num=$_REQUEST['num']?$_REQUEST['num']:"";
          $redirect=$_REQUEST['redirect'];
		  $pr_id=$_REQUEST['pr_id'] ? $_REQUEST['pr_id']: "";
		  $pr_email=$_REQUEST['pr_email'] ? $_REQUEST['pr_email']: "";
          $pr_phone=$_REQUEST['pr_phone'] ? $_REQUEST['pr_phone']: "";
		  $po_email=$_REQUEST['po_email'] ? $_REQUEST['po_email']: "";
          $po_phone=$_REQUEST['po_phone'] ? $_REQUEST['po_phone']: "";
		$image=$_FILES['school_logo'];
	  $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
	if($num){
	foreach($num as $row){
	$k=$row;
	$auth_num.=$k.',';
	}}
	$auth_num=rtrim($auth_num, ',');
	if($url){	
          $sth=$conn->prepare('update school set school_name=:school_name,school_logo=:url,address=:address,status=1,authority_numbers=:auth_num,city=:city,zipcode=:zipcode, country=:country,police_email=:po_email,police_contact=:po_phone,created_on=:signup_date where id=:id');
          }
        else{
          $sth=$conn->prepare('update school set school_name=:school_name,address=:address,status=1,authority_numbers=:auth_num,city=:city,zipcode=:zipcode, country=:country, police_email=:po_email,police_contact=:po_phone,created_on=:signup_date where id=:id');
          }
          $sth->bindValue("school_name",$s_name);
          $sth->bindValue("address",$address);
          $sth->bindValue("city",$city);
          $sth->bindValue("zipcode",$zipcode);
          $sth->bindValue("country",$country);
		  $sth->bindValue("id",$id);
           $sth->bindValue("auth_num",$auth_num);
          $sth->bindValue("po_email",$po_email);
          $sth->bindValue("po_phone",$po_phone);
          if($url) $sth->bindValue("url",$url);
          $sth->bindValue("signup_date",$signup_date);
          $count=0;
          try{$count=$sth->execute();
		  $success=1;
		  $msg="School Details Updated";
			}
          catch(Exception $e){
           echo $e->getMessage();
          }
		 for($p=0;$p<$i;$p++){ 
	$sth=$conn->prepare('update school_admin set principal_email=:pr_email,principal_phone=:pr_phone where id=:id');
         $sth->bindValue("pr_email",$pr_email[$p]);
		  $sth->bindValue("id",$pr_id[$p]);
          $sth->bindValue("pr_phone",$pr_phone[$p]);
          $count=0;
          try{$count=$sth->execute();}
          catch(Exception $e){
           // echo $e->getMessage();
          }
		  }
	
          header("Location: $redirect?success=$success&msg=$msg");
          break;     
     
	 case "update_status":
	 //print_r($_REQUEST);
	 $status=$_REQUEST['status'];
	 $sid=$_REQUEST['id'];
	 $sql="update school set status=:status where id=:id";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('status',$status);
	 $sth->bindValue('id',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){
	 echo $e->getMessage();
	 }
	 
	 	 $sql="update school_admin set status=:status where school_id=:sid ";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('status',$status);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){
	  echo $e->getMessage();
	 }
	 
	 $sql="update users set status=:status where school_id=:sid ";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('status',$status);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){
	  echo $e->getMessage();
	 }
		
	header("Location: list_schools.php");
	break;
	 
	  case "delete-school":
	 //print_r($_REQUEST);
	 $sid=$_REQUEST['school_id'];
	 
	   
	 $message['msg']="User Deleted";
	 $message['status']=6;
	 
	 $sql="select * from users where school_id=:sid";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
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
		
	   }
	 $lid=1;
	 $message['room']='1';
	 
	 $sql="delete from school where id=:sid";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();
	 if(!empty($reg_ids)){	
	GCM::send_notification($reg_ids, $message);
	    	}
	
	if(!empty($apn_id)){
		$apns->newMessage($apn_ids);
		$apns->addMessageAlert($message['msg']);
                $apns->addMessageCustom('l', $lid);
                $apns->addMessageCustom('r', $message['room']);
                $apns->addMessageCustom('s', $message['status']);
		$apns->queueMessage();
		$apns->processQueue();
    		}
	 }
	 catch(Exception $e){}
	 
	 $sql="delete from school_admin where school_id=:sid ";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){}
	 
	 $sql="delete from users where school_id=:sid ";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){}
		
	header("Location: list_schools.php");
	break;
	
	  case "delete-school-admin":
	 //print_r($_REQUEST);die;
	 $sid=$_REQUEST['id'];
	 $sql="delete from school_admin where id=:sid";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('sid',$sid);
	 try{$sth->execute();}
	 catch(Exception $e){
	 echo $e->getMessage();
	 }
	 		
	header("Location: list_schools.php");
	break;
	 
	case "signout":
	session_start();
	unset($_SESSION);
	session_destroy();
	header("Location: index.php?success=1&msg=Signout Successful!");
	break;
		
	case "reset-password":
		$token=$_REQUEST["token"];
		$password=$_REQUEST["password"];
		$confirm=$_REQUEST["confirm"];
		$redirect="../reset-password.php";
		if($password==$confirm){
			$sth=$conn->prepare("update users set access_token=:access_token where access_token =:token");
			$sth->bindValue("token",$token);
			$sth->bindValue("access_token",$password);
			$count=0;
			try{$count=$sth->execute();}
			catch(Exception $e){echo $e->getMessage();}
			if($count){
				$success=1;
				$msg="Password changed successfully";
				$base=$redirect.'?success='.$success.'&msg='.$msg;
			}
		}else{
			$success=0;
			$msg="Passwords didn't match";
			$base=$redirect.'?token='.$token.'&success='.$success.'&msg='.$msg;
		}
	header("Location: $base");
	break;
			
	case "change-password":
	
		$success=$msg=null;
		$redirect=$_REQUEST['redirect'];
		$oldpass=$_REQUEST['oldpass'];
		$newpass=$_REQUEST['newpass'];
		
		$sth=$conn->prepare("select * from admin where password=:password");
		$sth->bindValue("password",md5($oldpass));
		
		try{$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result) && $newpass && ($newpass==$_REQUEST['confirm'])){
			$newpass=md5($newpass);
			$sth=$conn->prepare("update admin set password=:password where name=:username");
			$sth->bindValue("username",'admin');
			$sth->bindValue("password",$newpass);
			$count=0;
			try{$count=$sth->execute();}catch(Exception $e){
			echo $e->getMessage();
			}
			if($count){
				$success=1;
				$msg="Password Updated!";
			}
			else{
				$success=0;
				$msg="Invalid Request! Try Again Later!";
				$redirect="changePassword.php";
			}
		}
		else{
			$success=0;
			
			/*if($newpass) $msg="All Fields are required!"; else */
			$msg="Passwords didn't match!";
			$redirect="changePassword.php";
		}
		
		
		header("Location: $redirect?success=$success&msg=$msg");
		break;
	
	
		
}	
?>