<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php 
$sid=$_REQUEST['id'];
$sth=$conn->prepare("select * from school where id=:id");
$sth->bindValue('id',$sid);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);

$sth=$conn->prepare("select * from school_admin where school_id=:sid");
$sth->bindValue('sid',$sid);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$res1=$sth->fetchAll(PDO::FETCH_ASSOC);

?>
 
<!DOCTYPE html>
  <html>
  <head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <style>
  .mtb{
  padding-bottom:10px;
  border-bottom:1px solid #CFC7C7;
  }
  </style>
    
    <title>Lockdown| Edit School</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
	<link href="../assets/css/sweetalert.css" rel="stylesheet" type="text/css" />
	
  <body>
      <?php require_once "../php_include/admin_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Edit School
            <a href="#lockdown_data" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#lockdown_data" >Lockdown Data</a> 
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Edit School </a></li>
            
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
			<input type="text" class="form-control" id="name" name="school_name" value="<?php echo $res[0]['school_name']; ?>" placeholder="School Name">
			</div>
		</div>
          </div>
		  
		 <!--  <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">School Logo</label>
			<div class="col-sm-8 col-sm-offset-1 image-upload">
			 
			 <?php if($res[0]['school_logo']){ ?>
			  <img src="../uploads/<?php echo $res[0]['school_logo']; ?>" class="def-image" style="width: 100%;height: 185px; padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;">
			 <?php } else{ ?>
			 <img src="../uploads/1407398656noDefaultImage6.gif" class="def-image" style="width: 100%;height: 185px; padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;">
			 <?php } ?>
			 <input type="file" name="school_logo" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
			</div>
		</div>
          </div> -->
		  
		   <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Address </label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="address" value="<?php echo $res[0]['address']; ?>" placeholder="School Address ">
			</div>
		</div>
          </div>
		  
		 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">City</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="city" value="<?php echo $res[0]['city']; ?>" placeholder="City">
			</div>
		</div>
          </div> 

	 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Zipcode</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="zipcode" value="<?php echo $res[0]['zipcode']; ?>" placeholder="Zipcode">
			</div>
		</div>
          </div>
          
          <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Signup Date</label>
			<div class="col-sm-10">
			<?#php $dt=date_format($res[0]['created_on'],'Y-m-d');?>
			<input type="text" class="form-control" name="signup_date" value="<?php echo $res[0]['created_on']; ?>" placeholder="Signup Date">
			</div>
		</div>
          </div>

	 <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Country</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="country" value="<?php echo $res[0]['country']; ?>" placeholder="Country">
			</div>
		</div>
          </div>  

		  <?php 
		  $pat=explode(',',$res[0]['authority_numbers']);
		  ?>
		<div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			
			<label for="name" class="col-sm-2 control-label">Authorities Numbers<br>
			<!-- <span class="input-group-addon btn btn-primary add_field_button" style="display: inline-table;"><i class="fa fa-plus"></i></span> -->
			</label>
			
			<div class="col-sm-10 input_fields_wrap">
			
			<?php foreach($pat as $row) { ?>
			<!--<div class="input-group"> -->
			<input type="text" class="form-control" name="num[]" value="<?php echo $row; ?>"  placeholder="Contact Details">
			<!-- <span class="input-group-addon btn btn-danger remove_field"><i class="fa fa-times"></i></span></span> -->
			<!-- </div> -->
			<?php } ?>
			
			</div>
		</div>
          </div> 
		</div>
      
	<div class="row mtb">
		  <div style="  font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;  padding-top: 10px;"> Administrator Details
			<?php if((count($res1)) >=2){ ?>
			<a class="btn btn-danger btn-sm" href="#myModal2" data-vid="<?php echo $row['sid'];?>" data-toggle="modal" data-target="#myModal2"> Delete</a>
			<?php } ?>
		  </div>
         
		<?php foreach($res1 as $row){ ?>
		 <div class="col-sm-12">
        <div class="form-group" style="margin-top:10px;">
			<label for="name" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
			<input type="hidden" name="pr_id[]" value="<?php echo $row['id']; ?>">
			<input type="text" class="form-control" name="pr_email[]" value="<?php echo $row['principal_email']; ?>" placeholder="Administrator Email">
			</div>
		</div>
          </div>
		  
		   <div class="col-sm-12">
          <div class="form-group" style="margin-top:10px;">
			<label for="name" class="col-sm-2 control-label">Phone Number</label>
			<div class="col-sm-10">
			<input type="text" class="form-control"  name="pr_phone[]" value="<?php echo $row['principal_phone']; ?>" placeholder="Administrator Contact Details">
			</div>
		</div>
          </div>
		  
		  <?php } ?>
		</div>
		
		
		<div class="row ">
		  <div style="font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;  padding-top: 10px;"> Local Police Details </div>
          <div class="col-sm-12">
        <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Police Email</label>
			<div class="col-sm-10">
			<input type="email" class="form-control" id="name" name="po_email" value="<?php echo $res[0]['police_email']; ?>" placeholder="Police Email">
			</div>
		</div>
          </div>
		  
		   <div class="col-sm-12">
          <div class="form-group" style="margin-top:20px;">
			<label for="name" class="col-sm-2 control-label">Police Contact</label>
			<div class="col-sm-10">
			<input type="text" class="form-control" name="po_phone" placeholder="Police Contact Number" value="<?php echo $res[0]['police_contact']; ?>" >
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
              <input type="hidden" name="event" value="edit-school">
              <input type="hidden" name="school_id" value="<?php echo $sid; ?>">
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
		<script src="../assets/js/sweet-alert.js" type="text/javascript"></script>
		
	<script type="text/javascript" src="../assets/js/html2canvas.js"></script>
		<script type="text/javascript" src="../assets/js/tableExport.js"></script>
		<script type="text/javascript" src="../assets/js/jquery.base64.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/base64.js"></script>	
		
 <script>
$("[data-mask]").inputmask();
</script>
<script>
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="new_field" style=" margin-top: 5px;"><div class="input-group"><input type="text" class="form-control" name="num[]" pattern="[7-9][0-9]{9}" placeholder="Contact Details"><span class="input-group-addon btn btn-danger remove_field"><i class="fa fa-times"></i></span></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); 
		if(($('.input_fields_wrap .input-group').length)>1){
		$(this).parent('div').remove(); x--;
		}
		else{
		swal('Atleast One Authority Number Required');
		}
		//alert($(this).parent('div').length);
    })
});
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
	
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1em;">
            <div class="modal-header" style="background-color:#dd4b39; border-top-left-radius: 1em;
                        border-top-right-radius: 1em;">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel" style="color:white;">Do You Really Want To Delete a School administrator ?</h4>
            </div>
			<form action="eventHandler.php" method="post"> 
        <div class="modal-body" >
		<div class="form-group" style="margin-top:20px;">
			<h4 class="control-label">Select School Administrator</h4>
			<select name="id" class="form-control">
			<?php foreach($res1 as $row){ ?>
			<option value="<?php echo $row['id']; ?>" > <?php echo $row['principal_email']; ?>  </option>
			<?php } ?>
			</select>
		</div>
		<input type="hidden" name="event" value="delete-school-admin">
               <div id="inside2" style="text-align:right;">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Remove</button>    
               </div>
        </div>
		</form>
        </div>
    </div> 
</div> 


<?php require_once('lockdown.php'); ?>	
	
  </body>
  </html>
  