<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php require_once "../php-excel/class-excel-xml.inc.php" ?>

<?php 
$filterby = (int) (!isset($_GET["filterby"]) ? 0 : $_GET["filterby"]);
	$chk=$_GET['chk'];
	if($filterby==1)
	$er="and users.user_type='staff' ";
	elseif($filterby==2)
	$er="and users.user_type='warden'";	
	else
	$er="and 1";
	
	if($chk==1)
	$wf=" and users.online=1";	
	elseif($chk==2)
	$wf=" and users.online=0";
	else
	$wf="and 1";
	
$searchKey=$_REQUEST['key'];
if($searchKey){
$sd="and (name LIKE '%{$searchKey}%' or position LIKE '%{$searchKey}%' or room LIKE '%{$searchKey}%' or user_type LIKE '%{$searchKey}%') ";
}
else{
$sd="and 1";
}

$sth=$conn->prepare("select * from school join school_admin on school_admin.school_id=school.id where `token`=:access_token");
$sth->bindValue('access_token',$key);
try{$sth->execute();}
catch(Exception $e){}
$result=$sth->fetchAll(PDO::FETCH_ASSOC);
$sid=$result[0]['school_id'];
$sth=$conn->prepare("select * from users where school_id=:school_id $sd $er $wf");
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
    <title>Lockdown| Users</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/school_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Users
            <a href="add_user.php" class="btn btn-primary btn-sm fa fa-plus-circle"></a> 
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-users"></i> Users </a></li>
            
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
                  <h3 class="box-title col-md-2">Staff & Wardens</h3>
                   <div class="btn-group col-md-3">
				<div class="input-group">
				<!-- <div class="input-group-addon"><i class="fa fa-search"> </i></div> -->
				<input class="form-control" id="search" type="text" aria-controls="example1" placeholder="User Name/Position/Room/Type" value="">
				<span class="input-group-btn"><button id="btnenter" class="btn btn-primary btn-flat fa fa-search" style="line-height: 20px;" 
				onclick="window.location.href='?key='+(document.getElementById('search').value)"></button></span>
			</div>
				</div>
					
	<div class="col-md-2">
		<div id="example2_length" class="">
			<label > <select size="1" name="example2_length" class="form-control btn btn-default " aria-controls="example2" onchange="window.location.href='?&limit=<?php echo $limit;?>&page=1&chk=<?php echo $chk; ?>&filterby='+(this.options[this.selectedIndex].value);">
				
				<?php foreach(array('0'=>'User Type','1'=>'Staff','2'=>'Warden') as $r=>$s){
					echo "<option value='$r' ";
					if($r==$filterby) echo "selected";
					echo ">$s</option>";
				} ?>
			</select></label>
		</div>
	</div>
	
	<div class="col-md-2">
		<div id="example2_length" class="">
			<label > <select size="1" name="example2_length" class="form-control btn btn-default " aria-controls="example2" onchange="window.location.href='?&limit=<?php echo $limit;?>&page=1&filterby=<?php echo $filterby; ?>&chk='+(this.options[this.selectedIndex].value);">
				
				<?php foreach(array('0'=>'Status','1'=>'Active','2'=>'Inactive') as $r=>$s){
					echo "<option value='$r' ";
					if($r==$chk) echo "selected";
					echo ">$s</option>";
				} ?>
			</select></label>
		</div>
	</div>

			<div class="col-md-2">
                    <div class="btn-group">
			<button class="btn bg-olive btn-sm dropdown-toggle " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bars"></i> Export Table Data</button> 
					<ul class="dropdown-menu " role="menu">
						<li><a href="#" onclick="$('#user_table').tableExport({type:'csv',escape:'false'});"><i class="fa fa-file"></i>CSV</a></li>				
						<li><a href="#" onclick="$('#user_table').tableExport({type:'excel',escape:'false'});"><i class="fa fa-file-text-o"></i>XLS</a></li>
				<!-- <li><a href="#" onclick="$('#user_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});"><i class="fa fa-file-text"></i>PDF</a></li> -->
					</ul>
				</div>
                  </div>
                </div>
				<!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table id="user_table" class="table table-hover table-bordered exm-2">
                    <thead>
					<tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Position</th>
		      <th>Room</th>
		  <th>Number</th>
                      <th>User Type</th>
                     <!-- <th> Access Token </th> -->
					  <th>Edit</th>
					  <th>Delete</th>
					  <th>Status</th>
                    </tr>
					</thead>
					<tbody>
					<?php foreach($res as $row) { ?>
                    <tr>
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo $row['name']; ?></td>
                      <td><?php echo $row['email']; ?></td>
                      <td><?php echo ($row['position']?$row['position']:'-'); ?></td>
                      <td><?php echo ($row['room']?$row['room']:'-'); ?></td>
                      <td><?php echo ($row['cellphone']?$row['country_code'].$row['cellphone']:'-'); ?></td>
					   <td><?php echo $row['user_type']; ?></td>
					  <!--  <td><?php echo $row['access_token']; ?></td> -->
					    <td><div class="btn btn-primary btn-xs" >
                      <a href="edit_user.php?id=<?php echo $row['id'];?>" style="color:white"><i class="fa fa-pencil"></i></a></div></td>
                      <td><div class="btn btn-danger btn-xs" type="submit"><a href="#myModal2" class="evtdltsnd" 
                        data-vid="<?php echo $row['id'];?>" data-toggle="modal" data-target="#myModal2"  style="color:white" >
                        <i class="fa fa-trash-o "></i></a></div>
                      </td>
                      <td><?php if($row['online']==1) echo '<span class="label label-success">Active</span>'; 
					  else{ ?> 
					  <a href="functions.php?id=<?php echo $row['id'];?>&event=send-details" data-toggle="tooltip" data-placement="top" title="  Send Reminder to User" class="btn btn-warning btn-xs" ><i class="fa fa-circle-o"></i></a>
					  <?php } ?>
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
                    "bSort": true,
                    "bInfo": true,
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