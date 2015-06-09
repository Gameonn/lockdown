<?php
//this is an api to login users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$email=$_REQUEST['email'];
$access_token=$_REQUEST['access_token'];
$reg_id=$_REQUEST['reg_id']?$_REQUEST['reg_id']:"";
$apn_id=$_REQUEST['apn_id']?$_REQUEST['apn_id']:"";

if(!($email && $access_token && $email!='null')){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$sql="select * from users where (email=:email or alt_email=:email) and access_token=:token and status=1";
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	$sth->bindValue('token',$access_token);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	
	if(count($res)){
	$data = DataClass::get_profile($email,$access_token)? DataClass::get_profile($email,$access_token):[];
	
	if($data){
	if($apn_id)
	$sql="update users set apn_id='' where apn_id=:apn_id";
	elseif($reg_id)
	$sql="update users set reg_id='' where reg_id=:reg_id";
	
	$sth=$conn->prepare($sql);
	if($apn_id) $sth->bindValue('apn_id',$apn_id);
	if($reg_id) $sth->bindValue('reg_id',$reg_id);
	$count=0;
	try{$count=$sth->execute();}
	catch(Exception $e){
	/*echo $e->getMessage();*/
	} 
	
	
	$success=1;
	$msg="Login Successful";
	if($apn_id)
	$sql="update users set apn_id=:apn_id,online=1 where email=:email";
	elseif($reg_id)
	$sql="update users set reg_id=:reg_id,online=1 where email=:email";
	elseif(!($apn_id && $reg_id))
	$sql="update users set online=1 where email=:email";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	if($apn_id) $sth->bindValue('apn_id',$apn_id);
	if($reg_id) $sth->bindValue('reg_id',$reg_id);
	$count=0;
	try{$count=$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	} 
	}
	}
	else{
	$success=0;
	$msg="Invalid Email or Password";
	}

}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>