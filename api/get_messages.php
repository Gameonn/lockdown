<?php
//this is an api to get lockdown messages
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
	
	if(count($result)){
   	$sql="select message_text from messages where lockdown_id=:lockdown_id order by id DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue("lockdown_id",$lockdown_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$msgs=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($msgs)){
	$success=1;
	$msg="Messages Found";
	}
	else{
	$success=0;
	$msg="No Message Found";
	}
	}
	else{
	$success=0;
	$msg="Invalid access token";
	}
	
	//$msgs=$msgs?$msgs:[];
		
}


// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1)
echo json_encode(array("success"=>$success,"msg"=>$msg, "data"=>$msgs));
else
echo json_encode(array("success"=>$success,"msg"=>$msg));

  function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
    if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
    else return $r;
  }
?>