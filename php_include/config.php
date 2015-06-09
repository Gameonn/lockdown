<?php
//error_reporting(0);
$servername = $_SERVER['HTTP_HOST'];
$pathimg=$servername."/";
define("ROOT_PATH",$_SERVER['DOCUMENT_ROOT']);
define("UPLOAD_PATH","http://code-brew.com/projects/lockdown/");
define("BASE_PATH","http://code-brew.com/projects/lockdown/");

define("SERVER_OFFSET","18000");
$DB_HOST = 'localhost';
$DB_DATABASE = 'codebrew_lockdown';
$DB_USER = 'codebrew_super';
$DB_PASSWORD = 'core2duo';

//GCM
define("AUTH_KEY","AIzaSyACjX2XALt5slzDjJbaMoXO1wzFhj4ORuE");
define('PLAYSTORE_LINK','bit.ly/droid');
define('APPSTORE_LINK','bit.ly/ios');


define('SMTP_USER','pargat@code-brew.com');
define('SMTP_EMAIL','pargat@code-brew.com');
define('SMTP_PASSWORD','core2duo');
define('SMTP_NAME','Lockdown');
define('SMTP_HOST','mail.code-brew.com');
define('SMTP_PORT','25');