<?php
//this is an api to save lockdown pictures
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
$pic=$_FILES['pic'];
global $conn;

if(!($access_token && $lockdown_id && $pic)){
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
	 $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$pic['name']));
    	if(@move_uploaded_file($pic['tmp_name'], "../uploads/$randomFileName")){
	      $success="1";
	      $url=$randomFileName;
   	 }
   
	$sql="insert into pictures values(DEFAULT,:id,:pic,:user_id,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$lockdown_id);
	$sth->bindValue('pic',$url);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();
	$success=1;
	$msg="Picture Added to Lockdown";
	}
	catch(Exception $e){
	//echo $e->getMessage();
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

echo json_encode(array("success"=>$success,"msg"=>$msg));

  function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
    if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
    else return $r;
  }
?>