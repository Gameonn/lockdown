<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php 
$sth=$conn->prepare("select * from school join school_admin on school_admin.school_id=school.id where `token`=:access_token");
$sth->bindValue('access_token',$key);
try{$sth->execute();}
catch(Exception $e){}
$result=$sth->fetchAll(PDO::FETCH_ASSOC);
$sid=$result[0]['school_id'];
$sth=$conn->prepare("select lockdown.*,(select users.name from users where users.id=lockdown.user_id1) as u1,(select users.name from users where users.id=lockdown.user_id2) as u2,
CASE 
                  WHEN DATEDIFF(NOW(),lockdown.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),lockdown.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),lockdown.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),lockdown.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),lockdown.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),lockdown.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),lockdown.created_on)) ,' s ago')
                END as time_elapsed
from lockdown where school_id=:school_id and Date_format(lockdown.created_on,'%Y-%m-%d')= Date_format(NOW(),'%Y-%m-%d') order by lockdown.id DESC");
$sth->bindValue('school_id',$sid);
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
    <title>Lockdown</title>
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
            Lockdown 
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-unlock"></i> Lockdown </a></li>
            
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
				  <a class="btn btn-sm btn-warning" href="update_status.php?status=2">Activate </a>
				  <a class="btn btn-sm btn-danger" href="update_status.php?status=4">Deactivate </a>
				  
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
                      <th>Requested By</th>
                      <th>Activated By</th>
                      <th>Time Elapsed</th>
					  <th>Messages</th>
					  <th>Pictures</th>
					<!--  <th>Update Status</th> -->
					  <th>Status</th> 
                    </tr>
					</thead>
					<tbody>
					<?php foreach($res as $row) { ?>
                    <tr>
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo ($row['u1']?$row['u1']:"-"); ?></td>
                      <td><?php echo ($row['u2']?$row['u2']:"School Admin"); ?></td>
                      <td><?php echo $row['time_elapsed']; ?></td>
					<td> 
					<a href="lockdown_messages.php?id=<?php echo $row['id'];?>" style="color:white" class="btn btn-primary btn-xs"><i class="fa fa-envelope"></i></a>
					</td>
					  <td>
					  <a href="lockdown_pictures.php?id=<?php echo $row['id'];?>" style="color:white" class="btn btn-primary btn-xs"><i class="fa fa-picture-o"></i></a>
					  </td>
					  <!-- <td><?php if($row['status']==1){ ?>
					     <a href="update_status.php?lockdown_id=<?php echo $row['id'];?>&status=2" style="color:white"  data-toggle="tooltip" data-placement="right" title="  Activate Lockdown" class="btn btn-danger btn-xs">Activate</a>
					
					  <?php } elseif($row['status']==2){ ?>
						  <a href="update_status.php?lockdown_id=<?php echo $row['id'];?>&status=4" style="color:white"  data-toggle="tooltip" data-placement="right" title="  Deactivate Lockdown" class="btn btn-warning btn-xs">Deactivate</a>
						<?php }	elseif($row['status']==4){ 
						echo '<span class="label label-primary" data-toggle="tooltip" data-placement="right" title="  Already In Safe Mode" >Safe Mode</span>';
							 } ?>
					  </td> -->
                      <td><?php 
					  //if($row['status']==1 && $row['time_elapsed']>= '1d ago'){ echo '<span class="label label-primary">Safe Mode</span>'; }
					   //elseif($row['status']==2 && $row['time_elapsed']>= '1d ago'){ echo '<span class="label label-primary">Safe Mode</span>'; }
					  if($row['status']==1){ echo '<span class="label label-warning">Requested</span>'; }
					  elseif($row['status']==2){ echo '<span class="label label-danger">Activated</span>'; }
					  elseif($row['status']==4){ echo '<span class="label label-primary">Safe Mode</span>'; }
					  ?> 
					  </td> 
                    </tr>
					<?php } ?>
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
 <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1em;">
            <div class="modal-header" style="background-color:#dd4b39; border-top-left-radius: 1em;
                        border-top-right-radius: 1em;">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel" style="color:white;">Do You Really Want To Delete The User ?</h4>
            </div>
			<form action="functions.php" method="post"> 
        <div class="modal-body" >
		<h4>
		  Deleting it will remove all related data
		</h4>
		<input type="hidden" name="event" value="delete-user">
		<input type="hidden" name="user_id" id="vid" value=0>
               <div id="inside2" style="text-align:right;">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Save Changes</button>    
               </div>
        </div>
		</form>
        </div>
    </div> 
</div>  


  </body>
  </html>
   <script>
   $(".evtdltsnd").click(function(){
  var subcatid= $(this).data('vid');
    $(".modal-body #vid").val(subcatid);
});
 
   </script>