<?php
//this is an api to register users on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$access_token=$_REQUEST['access_token'];
$name=$_REQUEST['name'];
$position=$_REQUEST['position']?$_REQUEST['position']:"";
$room=$_REQUEST['room']?$_REQUEST['room']:"";
$phone=$_REQUEST['phone']?$_REQUEST['phone']:'';
$email=$_REQUEST['email']?$_REQUEST['email']:'';
$country_code=$_REQUEST['country_code']?$_REQUEST['country_code']:'';
 

global $conn;

if(!($access_token && $email)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

else{ 

	$uid = DataClass::get_user_id($access_token);
	if($uid){
 $sql="update users set country_code=:country_code,is_set=1,email=:email,name=:name,position=:position,room=:room,cellphone=:phone where id=:id";
 $sth=$conn->prepare($sql);
 $sth->bindValue("name",$name);
  $sth->bindValue("email",$email);
 $sth->bindValue('position',$position);
 $sth->bindValue('room',$room);
 $sth->bindValue('phone',$phone);
 $sth->bindValue('id',$uid);
  $sth->bindValue('country_code',$country_code);
 try{$sth->execute();
	$success=1;
	$msg="User info updated";
	$is_set='1';
 }	
 catch(Exception $e){
 echo $e->getMessage();
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
if($success==1){
echo json_encode(array('success'=>$success,'msg'=>$msg,'is_set'=>$is_set));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>