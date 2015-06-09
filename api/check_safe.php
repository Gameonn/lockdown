<?php
//this is an api to check lockdown safe or not
//Lockdown status 1-pending, 2- approved, 3- rejected, 4- safe
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
$lockdown_id=$_REQUEST['lockdown_id'];
global $conn;

if(!($access_token && $lockdown_id)){
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
	
	if(count($result)){
	
	$data = DataClass::get_lockdown_status($school_id,$lockdown_id)? DataClass::get_lockdown_status($school_id,$lockdown_id):[];

	if($data){
	$success=1;
	if($data[0]['status']==1)
	$msg="Lockdown Requested";
	elseif($data[0]['status']==2)
	$msg="Lockdown Activated";
	elseif($data[0]['status']==3)
	$msg="Lockdown Rejected";
	elseif($data[0]['status']==4)
	$msg="Safe Mode";
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
echo json_encode(array("success"=>$success,"msg"=>$msg, "data"=>$data));
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>