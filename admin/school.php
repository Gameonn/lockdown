<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php 
$sid=$_REQUEST['school_id'];

$sth=$conn->prepare("select * from school where id=:id");
$sth->bindValue('id',$sid);
try{$sth->execute();}
catch(Exception $e){}
$res=$sth->fetchAll();

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
  .lead{
  font-size:17px;
  }
  </style>
    
    <title>Lockdown| School Details</title>
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
           School Details
		   <a href="edit_school.php?sc_id=<?php echo $sid; ?>"> <i class="fa fa-edit"> </i> </a>
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> School Details </a></li>
            
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
            
           <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
		  <div class="row mtb">
		  <div style="  font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;"> School Details </div>
		  
		     <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <tr>
                     <td class="lead">School Logo</td>
                      <td>	 <?php if($res[0]['school_logo']){ ?>
					  <img src="../uploads/<?php echo $res[0]['school_logo']; ?>" class="def-image" style="width: 100%;height: 185px; padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;">
					 <?php } else{ ?>
					 <img src="../uploads/1407398656noDefaultImage6.gif" class="def-image" style="width: 100%;height: 185px; padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;">
					 <?php } ?>
					 </td>
					  </tr>
					  <tr>
                      <td class="lead">School Name</td>
                      <td><?php echo $res[0]['school_name']; ?></td>
					  </tr>
					  <tr>
                      <td class="lead">Address</td>
					  <td> <?php echo $res[0]['address']; ?> </td>
                    </tr>
					
					  <tr>
                      <td class="lead">City</td>
					  <td> <?php echo $res[0]['city']; ?> </td>
                    </tr>
					
					  <tr>
                      <td class="lead">Zipcode</td>
					  <td> <?php echo $res[0]['zipcode']; ?> </td>
                    </tr>
					
					  <tr>
                      <td class="lead">Country</td>
					  <td> <?php echo $res[0]['country']; ?> </td>
                    </tr>
					
					  <tr>
                      <td class="lead">Authority Numbers</td>
					  <td> <?php echo $res[0]['authority_numbers']; ?> </td>
                    </tr>
               
                  </table>
                </div><!-- /.box-body -->
				  
		
	
		</div>
      
	<div class="row mtb">
		  <div style="  font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;  padding-top: 10px;"> Principal Details </div>
                <table class="table table-hover">
					<?php foreach($res1 as $row){ ?>
					<tr>
                      <td class="lead">Email</td>
                      <td><?php echo $row['principal_email']; ?></td>
					  </tr>
					  <tr>
                      <td class="lead">Contact Number</td>
					  <td> <?php echo $row['principal_phone']; ?> </td>
                    </tr>            
					<?php } ?>
                  </table>
		</div>
		
		<div class="row ">
		  <div style="font-size: 18px;font-family: sans-serif; font-weight: 400;letter-spacing: 1px;  padding-top: 10px;"> Local Police Details </div>
		  
		        <table class="table table-hover">
               
					  <tr>
                      <td class="lead">Police Email</td>
                      <td><?php echo $res[0]['police_email']; ?></td>
					  </tr>
					  <tr>
                      <td class="lead">Police Contact</td>
					  <td> <?php echo $res[0]['police_contact']; ?> </td>
                    </tr>            
                  </table>
		</div>
	  
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
		<script src="../assets/js/sweet-alert.js" type="text/javascript"></script>
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
  </body>
  </html>
  