<?php 
$sid=$_REQUEST['id'];
$sth=$conn->prepare("select lockdown.*,(select users.name from users where users.id=lockdown.user_id1) as u1,(select users.name from users where users.id=lockdown.user_id2) as u2,
CASE 
                  WHEN DATEDIFF(NOW(),lockdown.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),lockdown.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),lockdown.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),lockdown.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),lockdown.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),lockdown.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),lockdown.created_on)) ,' s ago')
                END as time_elapsed
from lockdown where school_id=:school_id order by lockdown.id DESC");
$sth->bindValue('school_id',$sid);
try{$sth->execute();}
catch(Exception $e){}
$lres=$sth->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="modal fade" id="lockdown_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1em;">
            <div class="modal-header" style="background-color:#dd4b39; border-top-left-radius: 1em;
                        border-top-right-radius: 1em;">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel" style="color:white;">Lockdown Data</h4>
            </div>
			
        <div class="modal-body" >
		   <div class="col-xs-12">
              <div class="box">
                 <div class="box-header">
                  <h3 class="box-title">Lockdown</h3>
				 
                  <div class="box-tools">
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
				<!-- /.box-header -->
                <div class="box-body table-responsive no-padding" style="max-height:700px;">
                  <table id="user_table" class="table table-hover table-bordered">
                    <thead>
					<tr>
                      <th>ID</th>
                      <th>Requested By</th>
                      <th>Activated By</th>
                      <th>Time Elapsed</th>
					<!--  <th>Messages</th>
					  <th>Pictures</th>-->
					<!--  <th>Update Status</th> -->
					  <th>Status</th> 
                    </tr>
					</thead>
					<tbody>
					<?php foreach($lres as $row) { ?>
                    <tr>
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo ($row['u1']?$row['u1']:"-"); ?></td>
                      <td><?php echo ($row['u2']?$row['u2']:"School Admin"); ?></td>
                      <td><?php echo $row['time_elapsed']; ?></td>
					<!-- <td> 
					<a href="lockdown_messages.php?id=<?php echo $row['id'];?>" style="color:white" class="btn btn-primary btn-xs"><i class="fa fa-envelope"></i></a>
					</td>
					  <td>
					  <a href="lockdown_pictures.php?id=<?php echo $row['id'];?>" style="color:white" class="btn btn-primary btn-xs"><i class="fa fa-picture-o"></i></a>
					  </td> -->
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
              </div><!-- /.box -->		</div>
		<input type="hidden" name="event" value="delete-school-admin">
               <div id="inside2" style="text-align:right;">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
               </div>
        </div>
		
        </div>
    </div> 
</div>