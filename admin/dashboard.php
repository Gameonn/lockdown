<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>

<?php 
$sql="select count(school.id) as cs, (select count(users.id) from users where user_type='staff') as sc, (select count(users.id) from users where user_type='warden') as wc from school";
$sth=$conn->prepare($sql);
try{$sth->execute();}
catch(Exception $e){}
$res=$sth->fetchAll();
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Lockdown| Dashboard</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
     </head>
<body>
            <?php require_once "../php_include/admin_leftmenu.php"; ?>
        <div class="content-wrapper">
            <!-- Left side column. contains the logo and sidebar -->
			<!-- right-side -->
                            
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<?php //error div
					if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
						<div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<?php echo $_REQUEST['msg']; ?>
						</div>
					<?php } // --./ error -- ?>
					
  <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-building-o"></i></span>
                <div class="info-box-content">
                  <span class="info-box-number"><?php echo $res[0]['cs']; ?></span>
                  <span class="info-box-text"><h4>Schools</h4></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
                        <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                 <span class="info-box-number"> <?php echo $res[0]['sc']; ?> </span>
                  <span class="info-box-text"><h4>Staff</h4></span>
                 
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                <span class="info-box-number"><?php echo $res[0]['wc']; ?></span>
                  <span class="info-box-text"><h4>Wardens</h4></span>
                  
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
          </div><!-- /.row -->

				
                </section><!-- /.content -->
        </div><!-- ./wrapper -->

        <!-- add new calendar event modal -->


        <!-- jQuery 2.0.2 -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- jQuery UI 1.10.3 -->
        <script src="../assets/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
        <!-- Bootstrap -->
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- Morris.js charts -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="../assets/js/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="../assets/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="../assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="../assets/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="../assets/js/app.js" type="text/javascript"></script>
		
		<!-- DATA TABES SCRIPT -->
        <script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		
    </body>
</html>
       