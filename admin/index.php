<?php session_start();
if(isset($_SESSION['admin'])){
header('location:dashboard.php');
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>LockDown | Log in</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="../assets/css/iCheck/square/blue.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnot work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo bg-olive" style=" margin-bottom: 0px; padding-bottom: 15px;padding-top: 15px;border-radius: 5px;">
       <img src="../uploads/lockdown_logo.png" style="width: 50px;">
	   Lockdown
      </div><!-- /.login-logo -->
      <div class="login-box-body">
      <?php //error div
                	if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
                		<div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
			            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			            	<?php echo $_REQUEST['msg']; ?>
			            </div>
			        <?php } // --./ error -- ?>
        <p class="login-box-msg lead">Sign in </p>
        <form action="eventHandler.php" method="post">
          <div class="form-group has-feedback">
            <input type="email" class="form-control" name="email" placeholder="Email" required/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password" placeholder="Password" required/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
             <input type="hidden" name="event" value="signin">
			  <input type="hidden" name="redirect" value="dashboard.php">
            <div class="col-xs-offset-8 col-xs-4">
              <button type="submit" class="btn bg-olive btn-block btn-flat">Sign In</button>
			
            </div><!-- /.col -->
          </div>
        </form>

      <!--  <a href="#">I forgot my password</a><br> -->

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="../assets/css/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="../assets/css/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>