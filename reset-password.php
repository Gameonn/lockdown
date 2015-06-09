<?php require_once "php_include/db_connection.php"; 
$token=$_REQUEST["token"];

$sth=$conn->prepare("select id from users where access_token=:token");
$sth->bindValue("token",$token);
$sth->execute();
$result=$sth->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lockdown| Reset Password</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo bg-olive" style=" margin-bottom: 0px; padding-bottom: 15px;padding-top: 15px;border-radius: 5px;">
       <img src="uploads/lockdown_logo.png" style="width: 50px;">
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
        <p class="login-box-msg lead">Reset Password </p>
        <form action="admin/eventHandler.php" method="post">
          <div class="form-group has-feedback">
       <input class="form-control" type="password" name="password" placeholder="Enter new password..">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
                 <input class="form-control" type="password" name="confirm" placeholder="Confirm password.."><br>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
             <input type="hidden" name="event" value="reset-password">
        <input type="hidden" name="redirect" value="../reset-password.php">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
            <div class="col-xs-offset-8 col-xs-4">

                    <button type="submit" class="btn bg-olive btn-block" onclick="return 0;">Submit</button>
        </div><!-- /.col -->
          </div>
        </form>

      <!--  <a href="#">I forgot my password</a><br> -->

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="assets/css/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="assets/css/iCheck/icheck.min.js" type="text/javascript"></script>
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