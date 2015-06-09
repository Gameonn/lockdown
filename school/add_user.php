<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php
$sth=$conn->prepare("select * from school join school_admin on school_admin.school_id=school.id where `token`=:access_token");
$sth->bindValue('access_token',$key);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);
$sid=$res[0]['school_id'];

$email=$_REQUEST['email'];
$name=$_REQUEST['name'];
$room=$_REQUEST['room'];
$position=$_REQUEST['position'];
$sms=$_REQUEST['sms'];
$cc=$_REQUEST['cc'];
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
    <title>Lockdown| Add User</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/school_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Add User
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Add User </a></li>
            
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
		  <div class="mtb" style="font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;"> Staff/Warden Details </div>
          <div class="col-sm-12 form-group">
        <div class="" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $name; ?>" required>
			</div>
		</div>
          </div>
		    
		   <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Position </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="position" value="<?php echo $position; ?>" placeholder="Position">
			</div>
		</div>
          </div>
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Room Number </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="room" value="<?php echo $room; ?>" placeholder="Room Number" required>
			</div>
		</div>
          </div>
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">SMS Number </label>
			<div class="col-sm-3">
			<input type="" class="form-control" id="country_code" title="Please enter valid area code" value="<?php echo $cc; ?>" minlength="1" maxlength="4" name="country_code" placeholder="Area Code" required>
			</div>
			<div class="col-sm-7">
			<input type="text" class="form-control" name="cellphone" id="phone_sms" value="<?php echo $sms; ?>" title="Please enter valid phone number" placeholder="SMS Number" required>
			</div>
		</div>
          </div>
		   
		<!--  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Landline Number </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="landline" placeholder="Landline Number">
			</div>
		</div>
          </div> -->
		  
		  <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Primary Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Primary Email" required>
			</div>
		</div>
          </div> 
		  
		 <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">Alt. Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" name="email2" placeholder="Secondary Email">
			</div>
		</div>
          </div> 

	 <div class="col-sm-12 form-group">
        <div class="" style="">
			<label for="name" class="col-sm-2 control-label">User Type</label>
			<div class="col-sm-10">
			<select class="form-control user_select" name="user_type">
			<option value="warden">Warden</option>
                        <option value="staff">Staff</option>
            </select>
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
			  <input type="hidden" name="school_id" value="<?php echo $sid; ?>">
			    <input type="hidden" name="token" value="<?php echo $key; ?>">
              <input type="hidden" name="event" value="add-user">
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
  