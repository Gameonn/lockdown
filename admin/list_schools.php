<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php
$searchKey=$_REQUEST['key'];
if($searchKey){
$sd="where school_name LIKE '%{$searchKey}%' ";
}
else{
$sd="where 1";
}

 
$sth=$conn->prepare("select school.*,date_format(school.created_on,'%Y-%m-%d') as signup_date,(select lockdown.status from lockdown where lockdown.school_id=school.id order by lockdown.created_on DESC Limit 1) as l_status,school.id as sid,(select date_format(lockdown.created_on,'%Y-%m-%d') from lockdown where lockdown.school_id=school.id order by lockdown.created_on DESC limit 1) as lock_date,(select group_concat(school_admin.principal_email SEPARATOR ',')  from school_admin where school_id=school.id ) as principal_email,(select group_concat(school_admin.principal_phone SEPARATOR ',')  from school_admin where school_id=school.id)  as principal_phone  from school join school_admin on school_admin.school_id=school.id $sd  group by school.id order by school.id ASC");
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
    <title>Lockdown| School</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/admin_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            All Schools 
			<a href="add_school.php" class="btn btn-primary btn-sm fa fa-plus-circle"></a> 
          </h1>
		  
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> All Schools </a></li>
            
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
                  <h3 class="box-title col-md-3">School Details</h3>
                  <div class="btn-group col-md-3">
				<div class="input-group" >
				<input class="form-control" id="search" type="text" aria-controls="example1" placeholder="Search by School Name" value="">
				<span class="input-group-btn"><button id="btnenter" class="btn btn-primary btn-flat fa fa-search" style="line-height: 20px;" 
				onclick="window.location.href='?key='+(document.getElementById('search').value)"></button></span>
			</div>
				</div>
                  
                  <div class="col-md-3 col-md-offset-3">
                    <div class="btn-group">
			<button class="btn bg-olive btn-sm dropdown-toggle " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bars"></i> Export 	Table Data</button> 
					<ul class="dropdown-menu " role="menu">
						<li><a href="#" onclick="$('#user_table').tableExport({type:'csv',escape:'false'});"><i class="fa fa-file"></i>CSV</a></li>				
						<li><a href="#" onclick="$('#user_table').tableExport({type:'excel',escape:'false'});"><i class="fa fa-file-text-o"></i>XLS</a></li>
				<!-- <li><a href="#" onclick="$('#user_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});"><i class="fa fa-file-text"></i>PDF</a></li> -->
					</ul>
				</div>
			
                  </div>
                </div>
                <div class="box-body table-responsive no-padding">
                  <table id="example2" class="table table-hover table-bordered">
                    <thead>
		     <tr>
                      <th>ID</th>
                      <th>School Name</th>
                      <th>Administrator Email</th>
                      <th>Administrator Contact</th>
                      <th>City</th>
                      <th>Signup Date </th>
                      <th>Last Lockdown Date </th>
                      <th>Lockdown Status</th>
		      <th>Inactive/Active</th>
		      <th>Edit</th>
		      <th>Delete</th>
		      <th>Status</th>
                    </tr>
			</thead>
			<tbody>
					<?php $r=1;
					foreach($res as $row) {	?>
                   	 <tr>
                      <td><?php echo $r; ?></td>
                      <td> <?php echo $row['school_name']; ?></td>
                      <td><?php 
					  $px=explode(',', $row['principal_email']);
					  foreach($px as $r1){
					  echo $r1.'  <br>';
						} ?></td>
                      <td><?php
					$px1=explode(',', $row['principal_phone']);
					  foreach($px1 as $r2){
					  echo $r2.' <br>';
						} ?></td>
					   <td><?php echo $row['city']; ?></td>
					   
					      <td><?php echo ($row['signup_date']?$row['signup_date']:'-'); ?></td>
					      <td><?php echo ($row['lock_date']?$row['lock_date']:'No Recent Lockdown'); ?></td>
					      
					         <td><?php 
					  if($row['l_status']==1){ echo '<span class="label label-warning">Requested</span>'; }
					  elseif($row['l_status']==2){ echo '<span class="label label-danger">Activated</span>'; }
					  elseif($row['l_status']==4){ echo '<span class="label label-primary">Safe Mode</span>'; }
					  else{ echo '<span class="label label-primary">Safe Mode</span>'; }
					  ?> 
					  </td>
					      
					   <td>     <?php if($row['status']){ ?> 
                           
                            <div class="btn btn-danger btn-xs"><a href="eventHandler.php?id=<?php echo $row['sid'];?>&event=update_status&status=0" data-toggle="tooltip" data-placement="right" title="  Set Inactive" 
                          style="color:white;"><i class="fa fa-ban"></i></a></div>

                        <?php } else{ ?>
                          <div class="btn btn-success btn-xs" ><a href="eventHandler.php?id=<?php echo $row['sid'];?>&event=update_status&status=1" data-toggle="tooltip" data-placement="right" title="  Set Active"
                          style="color:white;"><i class="fa fa-check"></i></a></div>
                          <?php } ?> </td>
					     <td><div class="btn btn-primary btn-xs" >
                      <a href="edit_school.php?id=<?php echo $row['sid'];?>" style="color:white"><i class="fa fa-pencil"></i></a></div></td>
                      <td><div class="btn btn-danger btn-xs" type="submit"><a href="#myModal2" class="evtdltsnd" 
                        data-vid="<?php echo $row['sid'];?>" data-toggle="modal" data-target="#myModal2"  style="color:white" >
                        <i class="fa fa-trash-o "></i></a></div>
                      </td>
                      <td><?php if($row['status']==1) echo '<span style="color:green;"><i class="fa fa-check"></i></span>'; else echo '<span style="color:red;"><i class="fa fa-times"></i></span>'; ?> </td> 
                    </tr>
					<?php $r=$r+1; } ?>
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
             /*   $('#example2').dataTable({
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });*/
                
           $(function () {
        $("#example1").dataTable();
        $('#example2').dataTable({
          "bPaginate": false,
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false
        });
      });
            });
        </script>

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1em;">
            <div class="modal-header" style="background-color:#dd4b39; border-top-left-radius: 1em;
                        border-top-right-radius: 1em;">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel" style="color:white;">Do You Really Want To Delete The School ?</h4>
            </div>
			<form action="eventHandler.php" method="post"> 
        <div class="modal-body" >
		<h4>
		  Deleting it will remove all related data
		</h4>
		<input type="hidden" name="event" value="delete-school">
		<input type="hidden" name="school_id" id="vid" value=0>
               <div id="inside2" style="text-align:right;">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Remove</button>    
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