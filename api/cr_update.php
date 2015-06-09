<?php
//this is an api to request lockdown
//Lockdown status 1-pending, 2- approved, 3- rejected, 4- safe
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data          +
// +-----------------------------------+
$people=array(
"+91 9653783614"
);
$school_id=7;
$data = DataClass::sendcall($people,$school_id)? DataClass::sendcall($people,$school_id):[];

  /*$sql="update `lockdown` set status=4 where Date_format(created_on,'%Y-%m-%d')= Date_format(NOW(),'%Y-%m-%d')";
  $sth=$conn->prepare($sql);
  try{$sth->execute();
  $success=1;
  //$msg="Lockdown status updated";
  }
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  
  
  
// +-----------------------------------+
// + STEP 4: send json data        +
// +-----------------------------------+

//echo json_encode(array("success"=>$success,"msg"=>$msg));
?>