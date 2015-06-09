<?php

require_once("../php_include/config.php");
require_once("../GCM.php");

$reg_ids[]='APA91bFDxdILpmrcT3UY1h3Y4E-mew4p2yrfOfYBwr_VBVhMk4jUtQuhPSza8nSnBb62iJkgLAZKg3lDpcwLa1yIuh9VZ3xh4caFwe2_KCvy0FlqMP6t53TE1Rv3K_KWRcF1LJc6TbczV5YZBvrX9uL5uVpFq4bg7Q';

if(!empty($reg_ids)){
	$push_data=array('push_type'=>'6','data'=>array('message'=>'Dummy push to gcm user'));
		try{
			
			GCM::send_notification($reg_ids,$push_data);
			
		}catch(Exception $e){
		//echo $e->getMessage();
		}
}