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
      
    function generateRandomString($length = 5){
    //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters='abcdefghijklmnopqrstuvwxyz';
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
		$sth=$conn->prepare("select * from school_admin where principal_email=:email");
		$sth->bindValue("email",$user);
		try{$sth->execute();}catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		//print_r($result);die;
		if(count($result)){
			foreach($result as $row){
		
				if($row['password']==md5($password)){
					session_start();
					$success=1;
					
					$_SESSION['school']['id']=$row['id'];
					$_SESSION['school']['email']=$row['email'];
					$_SESSION['school']['token']=$row['token'];
				}
			}
		}
		if(!$success){
			$redirect="index.php";
			$msg="Invalid Username/Password";
		}
		header("Location: $redirect?success=$success&msg=$msg");
		break;
	     
           case "edit-school": 
          
          $success=0;
          $id=$_REQUEST['school_id'];
          $s_name=$_REQUEST['school_name'];
          $address=$_REQUEST['address']?$_REQUEST['address']:"";
          $city=$_REQUEST['city']?$_REQUEST['city']:"";
           $zipcode=$_REQUEST['zipcode']?$_REQUEST['zipcode']:"";
           $country=$_REQUEST['country']?$_REQUEST['country']:"";
            $num=$_REQUEST['num']?$_REQUEST['num']:"";
          $redirect=$_REQUEST['redirect'];
		  $pr_email=$_REQUEST['pr_email'] ? $_REQUEST['pr_email']: ""; 
		  $country_code=$_REQUEST['country_code'] ? $_REQUEST['country_code']: "";
          $pr_phone=$_REQUEST['pr_phone'] ? $_REQUEST['pr_phone']: "";
          $pr_phone=$country_code. $pr_phone;
		  
		  $po_email=$_REQUEST['po_email'] ? $_REQUEST['po_email']: "";
          $po_phone=$_REQUEST['po_phone'] ? $_REQUEST['po_phone']: "";
		$image=$_FILES['school_logo'];
		$token=$_REQUEST['token'];
		
	  $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
	if($num){
	foreach($num as $row){
	$k=$row;
	$auth_num.=$k.',';
	}
	}
	$auth_num=rtrim($auth_num, ',');
	if($url){	
          $sth=$conn->prepare('update school set school_name=:school_name,school_logo=:url,address=:address,status=1,authority_numbers=:auth_num,city=:city,zipcode=:zipcode, country=:country, police_email=:po_email,police_contact=:po_phone where id=:id');
          }
          else{
          $sth=$conn->prepare('update school set school_name=:school_name,address=:address,status=1,authority_numbers=:auth_num,city=:city,zipcode=:zipcode, country=:country, police_email=:po_email,police_contact=:po_phone where id=:id');
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
          $count=0;
          try{$count=$sth->execute();
	  $success=1;
	  $msg="School Details Updated";
			}
          catch(Exception $e){
            echo $e->getMessage();
          }
   	
		 $sth=$conn->prepare('update school_admin set principal_email=:pr_email,principal_phone=:pr_phone,status=1 where token=:token');
          $sth->bindValue("token",$token);
          $sth->bindValue("pr_email",$pr_email);
          $sth->bindValue("pr_phone",$pr_phone);
          $count=0;
          try{$count=$sth->execute();}
          catch(Exception $e){
            echo $e->getMessage();
          }
	
          header("Location: $redirect?success=$success&msg=$msg");
          break;
		
	case "signout":
		session_start();
		unset($_SESSION);
		session_destroy();
		header("Location: index.php?success=1&msg=Signout Successful!");
		break;
		
	 case 'forgot_password':
	  print_r($_REQUEST);die;
          $email=$_REQUEST['email'];
          $redirect='forgot_password.php';
          if(!($email)){
           $success="0";
           $msg="Incomplete Parameters";
         }
         else{
           $sql="select * from school where email=:email";
           $sth=$conn->prepare($sql);
           $sth->bindValue("email",$email);
           try{$sth->execute();}catch(Exception $e){
			//echo $e->getMessage();
           }
           $res=$sth->fetchAll();

           if(count($res)){ 
             $token=md5($email);
             $sql="update school set token=:token where email=:email";
             $sth=$conn->prepare($sql);
             $sth->bindValue("email",$email);
             $sth->bindValue("token",$token);
             $count=0;
             try{$count=$sth->execute();}catch(Exception $e){
			 //echo $e->getMessage();
             }
             if($count){
              $success="1";
              $msg="An email is sent to you";
              
              $sql="select username from school where email=:email";
              $sth=$conn->prepare($sql);
              $sth->bindValue('email',$email);
              try{ $sth->execute();}catch(Exception $e){}
              $result=$sth->fetchAll();
              $username=ucwords($result[0]['username']);
              sendEmail($email,"Lockdown - Recover Password",
                "<div style='font-size:20px;line-height:1.6;'>
                <p>Dear $username,</p>
                <br>
                <p>we have received your password reset request.</p>
                <p>Please follow the link below to set a new password:</p>
                <p><a href='".BASE_PATH."school/reset_password.php?token={$token}'>".BASE_PATH."school/reset_password.php?token={$token}</a></p>
                <p>In case you have any questions or have not requested to change your password, please reach out to admin@lockdown.com.</p>
                <br>
                <p>Best,</p>
                <p>Lockdown Users</p>
               
              </div>"
              ,SMTP_EMAIL);
            }else{
              $success="0";
              $msg="Error occurred";
            }
          }
          else{
            $success="0";
            $msg="Invalid Email ";
          }
        }
        header("Location: $redirect?success=$success&msg=$msg");
        break;
	
	case "reset-password":
		$token=$_REQUEST["token"];
		$password=$_REQUEST["password"];
		$confirm=$_REQUEST["confirm"];
		if($password==$confirm){
			$sth=$conn->prepare("update users set password=:password where token=:token");
			$sth->bindValue("token",$token);
			$sth->bindValue("password",md5($password));
			$count=0;
			try{$count=$sth->execute();}catch(Exception $e){echo $e;}
			if($count){
				$success=1;
				$msg="Password changed successfully";
				$base="http://www.lockdown.com";
			}
			}else{
				$success=0;
				$msg="Passwords didn't match";
			}
	header("Location: $base?success=$success&msg=$msg");
	break;
	
	case "send-details":
	$uid=$_REQUEST['id'];
	$redirect='users.php';
	$base=BASE_PATH;
	$add="school/";
	$sql="select * from users where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	$email=$res[0]['email'];
	$name=$res[0]['name'];
	$user_type=$res[0]['user_type'];
	$password=$res[0]['access_token'];
	try{
	$success='1';
	$msg="Reminder Sent";
		sendEmail($email,"Signin your account from app",
                "<div style='font-size:16px;line-height:1.4;'>
                <p>Dear $name,</p>
                You are registered to the lockdown system with following credentials<br>
				Email=>$email
			  <br>User Type=>$user_type
			  <br>Access Token=>$password 
              		<br>
	                <p>Best,</p>
	                <p>Lockdown Users</p>
               
              </div>"
              ,SMTP_EMAIL);
		}
			catch(Exception $e){}
	header("Location: $base$add$redirect?success=$success&msg=$msg");
	break;
	
	case "add-user":
	$playstore=PLAYSTORE_LINK;
	$appstore=APPSTORE_LINK;
	//print_r($_REQUEST);die;
	$user_type=$_REQUEST['user_type'];
	$school_id=$_REQUEST['school_id'];
	$email=$_REQUEST['email'];
	 $password=generateRandomString();
	$name=$_REQUEST['name'];
	$position=$_REQUEST['position']?$_REQUEST['position']:"";
	$room=$_REQUEST['room']?$_REQUEST['room']:"";
	
	$country_code=$_REQUEST['country_code']?$_REQUEST['country_code']:"";
	$cellphone=$_REQUEST['cellphone']?$_REQUEST['cellphone']:"";
	$landline=$_REQUEST['landline']?$_REQUEST['landline']:"";
	$email2=$_REQUEST['email2']?$_REQUEST['email2']:"";
	$token=$_REQUEST['token'];
	$redirect=$_REQUEST['redirect'];
	$gm=$country_code. $cellphone;
	$base=BASE_PATH;
	$add="school/";
			
		$sth=$conn->prepare("select * from users where email=:email ");
		$sth->bindValue("email",$email);	
		try{$sth->execute();}catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		if(count($result)){
		$success="0";
		if($email==$result[0]['email'])
			$msg="Email is already taken";
		}
		else{
	
		
		  $sql="select * from school where id=:id";
		  $sth=$conn->prepare($sql);
		  $sth->bindValue('id',$school_id);
		  try{$sth->execute();}
		  catch(Exception $e){}
		  $res3=$sth->fetchAll();
		  $s_name=$res3[0]['school_name'];
	
		$sql="insert into users(id,apn_id,reg_id,school_id,name,position,room,country_code,cellphone,landline,email,alt_email,password,access_token,user_type,online,status,is_set,created_on,updated_on) values(DEFAULT,'','',:school_id,:name,:position,:room,:country_code,:cellphone,:landline,:email,:email2,:password,:token,:user_type,0,1,0,NOW(),NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("school_id",$school_id);
		$sth->bindValue("name",$name);
		$sth->bindValue("position",$position);
		$sth->bindValue("email",$email);
		$sth->bindValue("password",md5(1));
		$sth->bindValue("token",$password);
		$sth->bindValue("user_type",$user_type);
		$sth->bindValue("room",$room);
		$sth->bindValue("email2",$email2);
		$sth->bindValue("country_code",$country_code);
		$sth->bindValue("cellphone",$cellphone);
		$sth->bindValue("landline",$landline);
		
		$count1=0;
		try{$count1=$sth->execute();}
		catch(Exception $e){
		echo $e->getMessage();
		}
		
		if($count1){
		$msg="User successfully registered.";
		$success='1';
		
		if($gm){
		$people=array(
		$gm=>$email
		);
		}
		$body="Lockdown login creds for $s_name, User: $email, Pass: $password, $playstore $appstore";
		
		
		/*$b1="<a href='$playstore'>Play Store </a> ";
		$b2="<a href='$appstore'>App Store </a>";*/
		
		/*if($people){
		try{
		sendSMS($people,$body);
	          }
	          catch(Exception $e){}
	     	 }  */
	       
		sendEmail($email,"Signin your account from app",
                "<div style='font-size:16px;line-height:1.4;'>
                <p>Dear $name,</p>
                You are registered to the lockdown system with following credentials<br>
				Username=>$email
			  <br>User Type=>$user_type
			  <br>Access Token=>$password
			  <br><a href='".PLAYSTORE_LINK."'>Play Store </a><br>
			<a href='".APPSTORE_LINK."'>App Store </a> 
              		<br>
	                <p>Best,</p>
	                <p>Lockdown Users</p>
               
              </div>"
              ,SMTP_EMAIL);
           	}  
           	}
		
		if($success){
		header("Location: $base$add$redirect?success=$success&msg=$msg");
		}
		else{
		$redirect="add_user.php";
		header("Location: $base$add$redirect?success=$success&msg=$msg&name=$name&room=$room&email=$email&position=$position&cc=$country_code&sms=$cellphone");
		}	
	
		
		break;
	
	case 'edit-user':
	//print_r($_REQUEST);die;
	$user_type=$_REQUEST['user_type'];
	$uid=$_REQUEST['user_id'];
	$email=$_REQUEST['email'];
	$name=$_REQUEST['name'];
	$position=$_REQUEST['position']?$_REQUEST['position']:"";
	$room=$_REQUEST['room']?$_REQUEST['room']:"";
	$country_code=$_REQUEST['country_code']?$_REQUEST['country_code']:"";
	$cellphone=$_REQUEST['cellphone']?$_REQUEST['cellphone']:"";
	$landline=$_REQUEST['landline']?$_REQUEST['landline']:"";
	$email2=$_REQUEST['email2']?$_REQUEST['email2']:"";
	$token=$_REQUEST['token'];
	$redirect=$_REQUEST['redirect'];
	$gm=$country_code. $cellphone;
	$base=BASE_PATH;
	$add="school/";
	$message=array();
	    		
	$message['msg']="User Type Changed";
	$message['status']=5;
	
	
	$sql="select * from users where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	$u_type=$res[0]['user_type'];
	$school_id=$res[0]['school_id'];
	$apn_id=$res[0]['apn_id'];
	$reg_id=$res[0]['reg_id'];
	//echo $reg_id;die;
	$reg_ids[]=$reg_id;
	
	$lid=1;
	$message['room']='1';
	$message['user_mode']=$user_type;
	
	
	$sth=$conn->prepare("select * from lockdown where `school_id`=:sid and (DATE(`lockdown`.created_on)=CURDATE() or DATE(`lockdown`.created_on)=CURDATE()-1) order by lockdown.created_on DESC limit 1");
	$sth->bindValue('sid',$school_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$res2=$sth->fetchAll();
	if(count($res2)){
	$st=$res2[0]['status'];
	}
	else{
	$st=4;
	}
	
	if($st==4){
	$sql="update users set name=:name,email=:email,user_type=:user_type,position=:position,room=:room,alt_email=:email2,country_code=:country_code,cellphone=:cellphone,landline=:landline where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("id",$uid);
	$sth->bindValue("name",$name);
	$sth->bindValue("position",$position);
	$sth->bindValue("email",$email);
	$sth->bindValue("user_type",$user_type);
	$sth->bindValue("room",$room);
	$sth->bindValue("country_code",$country_code);
	$sth->bindValue("cellphone",$cellphone);
	$sth->bindValue("landline",$landline);
	$sth->bindValue("email2",$email2);
	try{$sth->execute();
	if($u_type!=$user_type){
	
	if(!empty($reg_ids)){	
	GCM::send_notification($reg_ids, $message);
	    	}
	
	if(!empty($apn_id)){
		$apns->newMessage($apn_id);
		$apns->addMessageAlert($message['msg']);
                $apns->addMessageCustom('l', $lid);
                $apns->addMessageCustom('r', $message['room']);
                $apns->addMessageCustom('s', $message['status']);
		$apns->queueMessage();
		$apns->processQueue();
    		}
	}
	}
	catch(Exception $e){}
	}
	else{
	$sql="update users set name=:name,email=:email,position=:position,room=:room,alt_email=:email2,country_code=:country_code,cellphone=:cellphone,landline=:landline where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("id",$uid);
	$sth->bindValue("name",$name);
	$sth->bindValue("position",$position);
	$sth->bindValue("email",$email);
	$sth->bindValue("room",$room);
	$sth->bindValue("country_code",$country_code);
	$sth->bindValue("cellphone",$cellphone);
	$sth->bindValue("landline",$landline);
	$sth->bindValue("email2",$email2);
	try{$sth->execute();}
	catch(Exception $e){}
	}
		
		header("Location: $base$add$redirect?success=$success&msg=$msg");
	break;
	
	 case "delete-user":
	 
	 $base=BASE_PATH;
	$add="school/";
	$redirect="users.php";
	$uid=$_REQUEST['user_id'];
	 
	 $message['msg']="User Deleted";
	 $message['status']=6;
	 
	 $sql="select * from users where id=:id";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('id',$uid);
	 try{$sth->execute();}
	 catch(Exception $e){}
	 
	 $res=$sth->fetchAll();
	 $school_id=$res[0]['school_id'];
	 $apn_id=$res[0]['apn_id'];
	 $reg_id=$res[0]['reg_id'];
	 
	 $reg_ids[]=$reg_id;
	 $lid=1;
	 $message['room']='1';
	 
	 $sql="delete from users where id=:uid";
	 $sth=$conn->prepare($sql);
	 $sth->bindValue('uid',$uid);
	 try{$sth->execute();
	 if(!empty($reg_ids)){	
	GCM::send_notification($reg_ids, $message);
	    	}
	
	if(!empty($apn_id)){
		$apns->newMessage($apn_id);
		$apns->addMessageAlert($message['msg']);
                $apns->addMessageCustom('l', $lid);
                $apns->addMessageCustom('r', $message['room']);
                $apns->addMessageCustom('s', $message['status']);
		$apns->queueMessage();
		$apns->processQueue();
    		}
	 }
	 catch(Exception $e){}
	 	
	header("Location: $base$add$redirect");
	break;
	
	case "change-password":
	
		$success=$msg=0;
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