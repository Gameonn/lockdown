<?php
//this is an api to check lockdown request
//Lockdown status 1-pending, 2- approved, 3- rejected, 4- safe
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
$success=$msg="0";$data=array();
$user_mode="";
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$access_token=$_REQUEST['access_token']; 

global $conn;
$logout=0;
if(!($access_token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

else{ 
	$sql="select * from users where users.access_token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("token",$access_token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uid=$result[0]['id'];
	$school_id=$result[0]['school_id'];
	$user_mode=$result[0]['user_type'];
	if(count($result)){
	
	$data = DataClass::get_lockdown($school_id)? DataClass::get_lockdown($school_id):[];
	
	if($data){
	$success=1;
	$msg="Lockdown Request Found";
	}
	else{
	$success=0;
	$msg="No Lockdown Request Found";
	}
	}
	else{
	$success=0;
	$logout=1;
	$msg="Invalid access token";
	}
			
}


// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1)
echo json_encode(array("success"=>$success,"msg"=>$msg, "data"=>$data,"user_mode"=>$user_mode));
else
echo json_encode(array("success"=>$success,"msg"=>$msg,"logout"=>$logout,"user_mode"=>$user_mode));


?>