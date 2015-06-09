<?php
 
class DataClass{
	
public static function get_profile($email,$token){
	
	global $conn;
	$sql="SELECT users.id as uid,users.name,users.position,users.room,users.country_code, users.cellphone,users.landline, users.email, users.access_token, users.user_type,users.online,users.status,users.is_set,users.school_id,school.school_name, school.school_logo, school.address,school.city,school.zipcode,school.authority_numbers FROM `users` join school on school.id=users.school_id where (users.email=:email or users.alt_email=:email) and users.access_token=:token and users.status=1";
	$sth=$conn->prepare($sql);
	$sth->bindValue("email",$email);
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	if(count($result)){
	
		foreach($result as $key=>$value){
				if($value['uid']){
					$data[]=array(
					    "uid"=> $value['uid'],
					    "name"=> $value['name']?$value['name']:"",
				            "position"=> $value['position']?$value['position']:"",
				            "room"=> $value['room']?$value['room']:"",
				            "country_code"=> $value['country_code']?$value['country_code']:"",
				            "phone"=> $value['cellphone']?$value['cellphone']:"",
				            "landline"=> $value['landline']?$value['landline']:"",
				            "email"=> $value['email'],
				            "access_token"=> $value['access_token'],
				            "user_type"=> $value['user_type'],
				            "online"=> $value['online'],
				            "status"=> $value['status'],
				             "is_set"=> $value['is_set'],
				            "school_id"=> $value['school_id'],
				            "school_name"=> $value['school_name'],
				            "school_logo"=> $value['school_logo']?BASE_PATH . "timthumb.php?src=uploads/".$value['school_logo']:"",		           
				            "address"=>$value['address']?$value['address']:"",
				            "city"=> $value['city']?$value['city']:"",
				            "zipcode"=> $value['zipcode']?$value['zipcode']:"",
				            "authority_number"=> $value['authority_numbers']?$value['authority_numbers']:""
					);
				}
			}
	}
	return $data;
}

public static function get_user_id($token){

	global $conn;
	$sql="select * from users where users.access_token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	$uid=$result[0]['id'];
	return $uid;
}


public static function generateRandomString($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

public static function sendSMS($people,$body){
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

public static function sendcall($people,$sid){

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
   
   
	//foreach ($people as $to) {
	$call = $client->account->calls->create(
            $from, // The number of the phone initiating the call
            $to,  // The number of the phone receiving call
            'http://www.code-brew.com/projects/lockdown/api/voice1.xml' // The URL Twilio will request when the call is answered
        );
		//$client->account->sms_messages->create($from, $to, $body);
		//echo "Sent message to $name";
		//echo $to." ". $body;
//}
return true;
}

public static function sendEmail($email,$subjectMail,$bodyMail,$email_back){

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



public static function get_lockdown($school_id){
	
	global $conn;
	/*$sql="select school.school_name,school.authority_numbers, lockdown.id as lockdown_id,lockdown.status as l_status,lockdown.user_id1,lockdown.user_id2 from lockdown join school on school.id=lockdown.school_id where lockdown.school_id=:school_id and (DATE(`lockdown`.created_on)=CURDATE() or DATE(`lockdown`.created_on)=CURDATE()-1) order by lockdown.created_on DESC limit 1";*/
	$sql="select school.school_name,school.authority_numbers, lockdown.id as lockdown_id,lockdown.status as l_status,lockdown.user_id1,lockdown.user_id2 from lockdown join school on school.id=lockdown.school_id where lockdown.school_id=:school_id order by lockdown.created_on DESC limit 1";
	$sth=$conn->prepare($sql);
	$sth->bindValue("school_id",$school_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
	$lid=$result[0]['lockdown_id'];
	$uid=($result[0]['user_id2']?$result[0]['user_id2']:$result[0]['user_id1']);
	$sql="select users.name,users.email as user_email,users.cellphone,users.room,users.user_type from users where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$uid);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$res=$sth->fetchAll();
	}
	
	if($result){
	$data=array(
	    "name"=> $res[0]['name']?$res[0]['name']:"",
            "email"=> $res[0]['user_email']?$res[0]['user_email']:"",
            "cellphone"=> $res[0]['cellphone']?$res[0]['cellphone']:"",
            "room"=> $res[0]['room']?$res[0]['room']:"",
            //"user_mode"=> $res[0]['user_type']?$res[0]['user_type']:"",
            "school_name"=> $result[0]['school_name']?$result[0]['school_name']:"",
            "authority_number"=> $result[0]['authority_numbers']?$result[0]['authority_numbers']:"",
            "lockdown_id"=>$lid,
            "status"=> $result[0]['l_status']?$result[0]['l_status']:""
	);
	}
	return $data;

}

public static function get_lockdown_status($school_id,$lockdown_id){
	
	global $conn;
	$data=array();
	//$sql="select * from lockdown where lockdown.school_id=:school_id and (DATE(`lockdown`.created_on)=CURDATE() or DATE(`lockdown`.created_on)=CURDATE()-1) order by lockdown.id DESC LIMIT 1 ";
	$sql="select lockdown.status,school.authority_numbers as authority_number from lockdown join school on school.id=lockdown.school_id where lockdown.school_id=:school_id and lockdown.id=:lockdown_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("school_id",$school_id);
	$sth->bindValue('lockdown_id',$lockdown_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	//$l_status=$result[0]['status'];
	//$lid=$result[0]['id'];
	/*if($result){
	if($l_status==1){
	$sql="select * from users join lockdown on lockdown.user_id1=users.id where lockdown.id=:lid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('lid',$lid);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	$res=$sth->fetchAll();
	}
	elseif($l_status==2){
	$sql="select * from users join lockdown on lockdown.user_id2=users.id where lockdown.id=:lid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('lid',$lid);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	$res=$sth->fetchAll();
	}
	
	$data=array(
	    "lockdown_id"=> $lid,
            "name"=> $res[0]['name']?$res[0]['name']:"",
            "status"=> $l_status,
            "room"=> $res[0]['room']?$res[0]['room']:""
	);
	}*/
	return $result;

}

public static function get_lockdown_name($school_id,$lockdown_id){
	
	global $conn;
	$data=array();
	$sql="select users.name from lockdown join users on users.id=lockdown.user_id2 where lockdown.status=2 and lockdown.school_id=:school_id and lockdown.id=:lockdown_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("school_id",$school_id);
	$sth->bindValue('lockdown_id',$lockdown_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uname=$result[0]['name'];
	return $uname;

}

public static function get_lockdown_details($lockdown_id){
	
	global $conn;
	$sql="SELECT school.*,lockdown.*,pictures.*,messages.* from lockdown join school on school.id=lockdown.school_id left join pictures on pictures.lockdown_id=lockdown.id left join messages on messages.lockdown_id=lockdown.id WHERE lockdown.id=:lockdown_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("school_id",$school_id);
	$sth->bindValue("lockdown_id",$lockdown_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;

}
	
}	
	
?>	