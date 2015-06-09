<?php
//print_r($_SESSION);
require_once "../php_include/db_connection.php";
if(isset($_SESSION['school']) && isset($_SESSION['school']['id'])){

}
else{
	$success=0;
	$msg="Signed Out! Sign In Again!";
	header("Location: index.php?success=$success&msg=$msg");
}


$key=$_SESSION['school']['token'];

$sql="select * from school join school_admin on school_admin.school_id=school.id where token=:token";
$sth=$conn->prepare($sql);
$sth->bindValue('token',$key);
try{$sth->execute();}
catch(Exception $e){}
$res=$sth->fetchAll();
$sname=$res[0]['school_name'];

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lockdown| School</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="../assets/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="../assets/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
         <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
	
    	<link href="../assets/css/_all-skins.css" rel="stylesheet" type="text/css" />	
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesnot work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
      <body class="skin-red">
    <div class="wrapper">
      
      <header class="main-header">
        <!-- Logo -->
        
        <div class="logo">
        <b>Lockdown</b>
       
        </div>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="pull-left">
           <span class="btn btn-default btn-flat" style="margin-top: 8px;margin-right: 8px;"><?php echo $sname; ?></span>
                   
          </div>
          <div class="pull-right">
            <a href="../school/functions.php?event=signout" class="btn btn-default btn-flat" style="border-radius: 50px; margin-top: 8px;margin-right: 8px;">Sign out</a>
          </div>
        </nav>
      </header>