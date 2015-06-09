<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; 
$date=Date('Y-m-d');

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
    <title>Lockdown| Add School</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/admin_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Add School
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Add School </a></li>
            
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
            
            
            <form action="eventHandler.php" id="school_form" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
		  <div class="row mtb">
		  <div style="  font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;"> School Details </div>
          <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">School Name</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" id="name" name="school_name" title="Please enter only letters (hyphens and spaces may be included)" placeholder="School Name" required>
			</div>
		</div>
          </div>
		    
		   <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Address </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="address" placeholder="School Address ">
			</div>
		</div>
          </div>
		  
		 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">City</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="city" placeholder="City">
			</div>
		</div>
          </div> 

	 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Zipcode</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="zipcode" placeholder="Zipcode">
			</div>
		</div>
          </div>
          
           <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Signup Date</label>
			<div class="col-sm-10">
			<input type="date" class="form-control" name="signup_date" placeholder="Signup Date" value="<?php echo $date; ?>" >
			</div>
		</div>
          </div>

	 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Country</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="country" placeholder="Country">
			</div>
		</div>
          </div>  
 
		</div>
      
	<div class="row ">
		  <div style="  font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;  padding-top: 10px;"> Administrator Details </div>
          <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Administrator Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" id="pr_email" name="pr_email" placeholder="Administrator Email" required>
			</div>
		</div>
          </div>
		  
		   <div class="col-sm-12">
          <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Administrator Phone</label>
			
                      
                       <div class="col-sm-3">
			<input type="text" class="form-control" id="country_code" title="Please enter valid area code" minlength="1" maxlength="4" name="country_code" placeholder="Area Code" required>
			</div>
                                         
                      <div class="col-sm-7">
			<input type="text" class="form-control" id="phone" minlength="10" title="Please enter valid phone number"  name="pr_phone" placeholder="Administrator Contact Details" required>
			</div>
                
		</div>
          </div>
		</div>
		 <div class="col-sm-10 col-sm-offset-2 submit-btn-con" style="margin-top:20px;">
            <button type="submit" load-text="<i class='fa fa-spinner spin'></i> Saving..." class="load-btn btn btn-primary btn-block ">
            Save Changes</button>
          </div>
            </div>
          </div>
         
              <!-- hidden -->
              <input type="hidden" name="event" value="add-school">
              <input type="hidden" name="redirect" value="list_schools.php">
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
</script>

     <script type="text/javascript">

      $('.image-upload').on("change","input[type='file']",function () {
                // alert('hey');
                var files = this.files;
                var reader = new FileReader();
                name=this.value;
                var this_input=$(this);
                reader.onload = function (e) {

                 this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100 %').height('185px');
               }
               reader.readAsDataURL(files[0]);
             });

    </script> 
  </body>
  </html>
  