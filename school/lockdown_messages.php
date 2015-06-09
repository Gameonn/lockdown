<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php 
$lid=$_REQUEST['id'];
$sth=$conn->prepare("select messages.*,(select users.name from users where users.id=messages.user_id) as u1,(select users.user_type from users where users.id=messages.user_id) as u2,
CASE 
                  WHEN DATEDIFF(NOW(),messages.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),messages.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),messages.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),messages.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),messages.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),messages.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),messages.created_on)) ,' s ago')
                END as time_elapsed
from messages where lockdown_id=:lockdown_id order by messages.id DESC");
$sth->bindValue('lockdown_id',$lid);
try{$sth->execute();}
catch(Exception $e){}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);

?>
 <!DOCTYPE html>
  <html>
  <head>
  <style>
  .mtb{
  padding-bottom:10px;
  border-bottom:1px solid #CFC7C7;
  }
  </style>
    <meta charset="UTF-8">
    <title>Lockdown| Messages</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/school_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           <a class="fa fa-arrow-circle-left" href="lockdown.php"></a> Lockdown Messages
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-envelope"></i> Lockdown Messages</a></li>
            
          </ol>
        </section>   

        <!-- Main content -->
        <section class="content" >
            <?php //error div
            if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
            <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
              <?php echo $_REQUEST['msg']; ?>
            </div>
            <?php } // --./ error -- ?>
            
                <div class="row">
            <div class="col-xs-12">
              <div class="box">
                 <div class="box-header">
                  <h3 class="box-title">Lockdown</h3>
                  <div class="box-tools">
                   <!-- <div class="btn-group">
			<button class="btn bg-olive btn-sm dropdown-toggle " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bars"></i> Export 	Table Data</button> 
					<ul class="dropdown-menu " role="menu">
						<li><a href="#" onclick="$('#user_table').tableExport({type:'csv',escape:'false'});"><i class="fa fa-file"></i>CSV</a></li>				
						<li><a href="#" onclick="$('#user_table').tableExport({type:'excel',escape:'false'});"><i class="fa fa-file-text-o"></i>XLS</a></li>
				<!-- <li><a href="#" onclick="$('#user_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});"><i class="fa fa-file-text"></i>PDF</a></li> -->
				<!--	</ul>
				</div> -->
                  </div>
                </div>
				<!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table id="user_table" class="table table-hover table-bordered">
                    <thead>
					<tr>
                      <th>ID</th>
                      <th>Name</th>
					  <th> User Type</th>
                      <th>Message Text</th>
                      <th>Time Elapsed</th>
                    </tr>
					</thead>
					<tbody>
					<?php foreach($res as $row) { 
					$r=1;
					?>
                    <tr>
                      <td><?php echo $r; ?></td>
                      <td><?php echo ($row['u1']?$row['u1']:"-"); ?></td>
                      <td><?php echo ($row['u2']?$row['u2']:"-"); ?></td>
                      <td><?php echo ($row['message_text']?$row['message_text']:"-"); ?></td>
                      <td><?php echo $row['time_elapsed']; ?></td>
					</tr>
					<?php
					$r=$r+1;
					} ?>
                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
          </div>
          
          </section><!-- /.content -->
      </div><!-- ./wrapper -->


      <!-- jQuery 2.0.2 -->
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
      <!-- jQuery UI 1.10.3 -->
      <script src="../assets/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
      <!-- Bootstrap -->
      <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
      <!-- Bootstrap WYSIHTML5 -->
      <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>
      
      <!-- iCheck -->
      <script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

      <!-- AdminLTE App -->
      <script src="../assets/js/app.js" type="text/javascript"></script>
      
      <!-- DATA TABES SCRIPT -->
      <script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
      <script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
      <script src="../assets/js/bootstrap-multiselect.js" type="text/javascript"></script>
	          <!-- InputMask -->
        <script src="../assets/js/jquery.inputmask.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.extensions.js" type="text/javascript"></script>
 	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
 	<script src="../assets/js/jquery.validate.js" type="text/javascript"></script>
	
	  <script type="text/javascript" src="../assets/js/html2canvas.js"></script>
		<script type="text/javascript" src="../assets/js/tableExport.js"></script>
		<script type="text/javascript" src="../assets/js/jquery.base64.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/base64.js"></script>	
 <script>
$("[data-mask]").inputmask();
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});
</script>
	<script type="text/javascript">
            $(function() {
               //$("#user_table").dataTable();
                $('#user_table').dataTable({
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": false,
                    "bInfo": false,
                    "bAutoWidth": false
                });
            });
        </script>
  </body>
  </html>
  