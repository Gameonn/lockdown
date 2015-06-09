<?php
//this is an api to get lockdown pictures
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
$zone=$_REQUEST['zone']?$_REQUEST['zone']:19800;
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
	$pat=BASE_PATH ."timthumb.php?src=uploads/";
   	$sql="select CONCAT('$pat',pic) as image,FROM_UNIXTIME( UNIX_TIMESTAMP( `pictures`.created_on ) +".SERVER_OFFSET."+ ({$zone}) )  as pic_time from pictures where lockdown_id=:lockdown_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("lockdown_id",$lockdown_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$pics=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($pics)){
	$success=1;
	$msg="Pictures Found";
	foreach($pics as $key=>$value){
			$pic_date=date('Y-m-d',strtotime($value['pic_time']));
			$pic_time=date('H:i:s',strtotime($value['pic_time']));
			
					$data[]=array(
					    "image"=> $value['image'],
				            "date"=> $pic_date,
				            "time"=> $pic_time
					);
				}
			
	
	
	}
	else{
	$success=0;
	$msg="No Pictures Found";
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

  function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
    if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
    else return $r;
  }
?>