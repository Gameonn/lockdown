<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php
$sid=$_REQUEST['id'];
$sth=$conn->prepare("select * from users where `id`=:id");
$sth->bindValue('id',$sid);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);
$school_id=$res[0]['school_id'];


$sth=$conn->prepare("select * from lockdown where `school_id`=:sid and (DATE(`lockdown`.created_on)=CURDATE() or DATE(`lockdown`.created_on)=CURDATE()-1) order by lockdown.created_on DESC limit 1");
$sth->bindValue('sid',$school_id);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
if(count($res1)){
$st=$res1[0]['status'];
}
else{
$st=4;
}
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
    <title>Lockdown| Edit User</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/school_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Edit User
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Edit User </a></li>
            
          </ol>
        </section>   

        <!-- Main content -->
        <section class="content" style="text-align:center;">
            <?php //error div
            if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
            <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
              <?php echo $_REQUEST['msg']; ?>
            </div>
            <?php } // --./ error -- ?>
            
            
            <form action="functions.php" id="user_form" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
		  <div class="row mtb">
		  <div class="mtb" style="font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;"> User Details </div>
          <div class="col-sm-12 form-group">
        <div class="" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>" placeholder="Name" required>
			</div>
		</div>
          </div>
		    
		   <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Position </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="position" value="<?php echo $res[0]['position']; ?>" placeholder="Position">
			</div>
		</div>
          </div>
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Room Number </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="room" value="<?php echo $res[0]['room']; ?>" placeholder="Room Number" required>
			</div>
		</div>
          </div>
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">SMS Number </label>
			<div class="col-sm-3">
			<input type="" class="form-control" id="country_code" value="<?php echo $res[0]['country_code']; ?>" name="country_code" placeholder="Area Code" >
			</div>
			<div class="col-sm-7">
			<input type="text" class="form-control" name="cellphone" id="phone_sms" value="<?php echo $res[0]['cellphone']; ?>" placeholder="SMS Number" >
			</div>
		</div>
          </div>
		   
		  <!-- <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Landline Number </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="landline" value="<?php echo $res[0]['landline']; ?>" placeholder="Landline Number">
			</div>
		</div>
          </div> -->
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Primary Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>" placeholder="Primary Email" required>
			</div>
		</div>
          </div> 
		  
		 <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Alt. Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" name="email2" value="<?php echo $res[0]['alt_email']; ?>" placeholder="Secondary Email">
			</div>
		</div>
          </div> 

	 <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">User Type</label>
			<div class="col-sm-10">
			
			<?php if($st!=4) {?>
			<input type="hidden" name="user_type" value="<?php echo $res[0]['user_type']; ?>" >
			<?php } ?>
			
			<select class="form-control" name="user_type" <?php if($st!=4) echo 'disabled'; ?> >
			<option value="staff" <?php if($res[0]['user_type']=='staff') echo 'selected'; ?> >Staff</option>
                        <option value="warden" <?php if($res[0]['user_type']=='warden') echo 'selected'; ?> >Warden</option>
            		</select>
            		<p class="text-yellow">User Type can be changed only after Safe Mode</p>
			</div>
		</div>
          </div>

	 	</div>	  
           <div class="col-sm-10 col-sm-offset-2 submit-btn-con" style="margin-top:20px;">
            <button type="submit" class="load-btn btn btn-primary btn-block ">
            Save Changes</button>
          </div>
            </div>
          </div>
         
              <!-- hidden -->
			  <input type="hidden" name="user_id" value="<?php echo $sid; ?>">
			    <input type="hidden" name="token" value="<?php echo $key; ?>">
              <input type="hidden" name="event" value="edit-user">
              <input type="hidden" name="redirect" value="users.php">
            </form>

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
 <script>
$("[data-mask]").inputmask();
$('select').on('change', function() {

if(this.value=='staff'){
$('#phone_sms').attr('required',false);
$('#country_code').attr('required',false);
}
else{
$('#phone_sms').attr('required',true);
$('#country_code').attr('required',true);

}

});
</script>

 
  </body>
  </html>
  