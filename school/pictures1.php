<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/school_header.php"; ?>
<?php 
$lid=$_REQUEST['id'];
$sth=$conn->prepare("select pictures.*,(select users.name from users where users.id=pictures.user_id) as u1,(select users.user_type from users where users.id=pictures.user_id) as u2,
CASE 
                  WHEN DATEDIFF(NOW(),pictures.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),pictures.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),pictures.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),pictures.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),pictures.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),pictures.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),pictures.created_on)) ,' s ago')
                END as time_elapsed
from pictures where lockdown_id=:lockdown_id order by pictures.id DESC");
$sth->bindValue('lockdown_id',$lid);
try{$sth->execute();}
catch(Exception $e){}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);
//print_r($res);die;
?>
 <!DOCTYPE html>
  <html>
  <head>
   
  <style>

   @media(min-width: 914px) AND (max-width: 1100px) {
    .random>li {
        width:25% ;
    } }

    @media(min-width: 376px) AND (max-width: 913px) {
    .random>li {
        width:50% ;
    } }

   @media(max-width: 375px) {
    .random>li {
        width:100% ;
    } }       
     </style>
     <style>    
    
  .mtb{
  padding-bottom:10px;
  border-bottom:1px solid #CFC7C7;
  }

  
  </style>
  </head>
    <meta charset="UTF-8">
    <title>Lockdown Pictures</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/AdminLTE.min.css" rel="stylesheet" type="text/css" />
  <body>
      <?php require_once "../php_include/school_leftmenu.php"; ?>
    <div class="content-wrapper">               
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
             <a class="fa fa-arrow-circle-left" href="lockdown.php"></a> Lockdown Pictures
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-picture-o"></i> Lockdown Pictures </a></li>
            
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
            
               <div class="row" style="position: relative; border-radius: 3px; background: #ffffff;border-top: 3px solid #d2d6de; margin-bottom: 20px; width: 100%;
               box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);">

              <div class="box box-danger">
                 <div class="box-header">
                  <h3 class="box-title">Lockdown</h3>
                </div>   <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix col-md-12 random">
                   
                    <?php
                               foreach($res as $key=>$subarray){
                                
                                ?>
                                
                   <li  class="col-md-3">
                     <div >
                      <img src="../uploads/<?php echo $subarray['pic'];?>" height="120" width="120" class="img-circle">
                      <span class="users-list-date">
                      <?php echo $subarray['u1'];?>
                      </span>
                      <span class="users-list-date">
                      <?php echo $subarray['u2'];?>
                      </span>
                      <span class="users-list-date"><b><?php echo $subarray['time_elapsed']; ?></b></span>
                     </div>
                    </li>
                   
                    <?php 
                           }   
                              ?>
                </ul><!-- /.users-list -->
                </div><!-- /.box-body -->


        </div><!-- /.box -->
           
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
   