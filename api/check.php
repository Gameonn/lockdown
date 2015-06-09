<?php
//this is an api to request lockdown
//Lockdown status 1-pending, 2- approved, 3- rejected, 4- safe
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("DataClass.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_lockdown');
$db->show_errors();

$success=$msg="0";$data=array();$reg_ids=array();
$apn_ids=array();$people=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

					
	//$apns->processQueue();
						
	
?>