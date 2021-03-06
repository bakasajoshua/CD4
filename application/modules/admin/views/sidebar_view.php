<div>
	<div>
		<div class="section-title" ><center>
			Notifications 
			<div class="right">
			<i class="glyphicon glyphicon-exclamation-sign"></i>
			</div>
		</div>
		<div>
			<?php 
				if(sizeof($devices_not_reported)>0){
			?>
			<div class="notice">
				<a  href="#devicesnotreported" data-toggle="modal">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					<?php echo sizeof($devices_not_reported); ?> Devices Did not upload Last Month.
				</a>
			</div>			
			<?php 
				}else{
			?>
			<div class="success">
				<a  href="#devicesnotreported" data-toggle="modal">
					<i class="glyphicon glyphicon-ok"></i> 
					 All devices uploaded results for last month
				</a>
			</div>
			<?php 
				}
			?>
			<div class="notice">
				<a href="javascript:void(null);">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					<?php echo $errors_agg["error"]." Errors <b>(";if($errors_agg["total"]>0){echo round((($errors_agg["error"]/$errors_agg["total"])*100),2);}else{ echo "0";}?>%)</b> reported last month out of <?php echo $errors_agg["total"];?> tests
				</a>
			</div>
			<?php 
				if(sizeof($failed_uploads)>0){
			?>		
			<div class="notice">
				<a href="#unrecognizeddevices" data-toggle="modal">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					<?php echo sizeof($failed_uploads); ?> unrecognized devices tried to upload data
				</a>
			</div>
			<?php 
				}else{
			?>
			<div class="success">
				<a  href="#unrecognizeddevices" data-toggle="modal">
					<i class="glyphicon glyphicon-ok"></i> 
					No unrecognized devices tried to upload
				</a>
			</div>
			<?php 
				}
			?>
			<?php 
				if($requests != NULL){
					foreach ($totals as $total) {
			?>		
			<div class="notice">
				<a href="#requestedfacilitiees" data-toggle="modal">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					There are <?php echo $total['Request']; ?> facilities requested for registration.
				</a>
			</div>
			<?php 
				}}else{
			?>
			<div class="success">
				<a  href="#" data-toggle="modal">
					<i class="glyphicon glyphicon-ok"></i> 
					No facility has been requested for registration.
				</a>
			</div>
			<?php 
				}
			?>
		</div>
	</div>
	<div>
		<div class="section-title" ><center>
			Side Menu 
			<div class="right">
			<i class="glyphicon glyphicon-list"></i>
			</div>
		</div>
		<div>
			<div class="section-content">	
				<ul class="nice-list">
					<li><span class="quiet">1.</span> <a href="<?php echo base_url()?>user/profile">My Profile</a></li>
					<li><span class="quiet">2.</span> <a href="settings">Settings</a></li>
					<!-- <li><span class="quiet">3.</span> <a href="#SystemUploads" data-toggle="modal" onclick = "systemUpload()">Attempt a System Upload</a></li> -->
					<li><span class="quiet">3.</span> <a href="javascript:void(null);" onclick = "systemUpload()">Attempt a System Upload</a></li>			
					<li><span class="quiet">4.</span> <a href="#changePassword" data-toggle="modal">Change Password</a></li>
				</ul>					
			</div>
			<div class="modal fade" id="SystemUploads" data-backdrop="static" data-keboard="false" >
			  	<div class="modal-dialog" style="width:60%;margin-bottom:2px;">
			    	<div class="modal-content" >
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title">System Uploads</h4>
			      		</div>
			      		<div class="modal-body" id ="modalbody">
			       	 		<div class="alert alert-info">
							  Wait while data is being fetched and uploaded! This may take a few moments.
							</div>
			       	 		<div class="progress progress-striped active">
							  <div class="progress-bar "  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
							    <span class="sr-only">45% Complete</span>
							  </div>
							</div>
			      		</div>
			      		<div class="modal-footer" style="height:11px;padding-top:11px;">
			      			<?php echo $this->config->item("copyrights");?>
			      		</div>
			   		</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</div>
	</div>
	<div class="modal fade" id="devicesnotreported" >
	  	<div class="modal-dialog" style="width:37%;margin-bottom:2px;">
	    	<div class="modal-content" >
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title">Devices <?php 

	        		$user_group  = $this->session->userdata("user_group_id");

	        		if($user_group==3){
	        			echo " for ".$devices_not_reported[0]['partner']." "; 
	        		}elseif($user_group==8){
	        			echo " in ".$devices_not_reported[0]['district']." "; 
	        		}elseif($user_group==9){
	        			echo " in ".$devices_not_reported[0]['region']." "; 
	        		}
	        		
	        		?> 
	        		not reported for <?php echo Date("Y,F");?></h4>
	      		</div>
	      		<div class="modal-body" style="padding-bottom:0px;">
	            	<table id="data-table-side">
	            		<thead>				
							<th>#</th>
							<th>Facility</th>							
							<th>Equipment Type</th>							
						</thead>
						<tbody>
							<?php
								$i=1;
								foreach ($devices_not_reported as $equipment) {
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $equipment['facility_name'];?></td>
								<td>
									<?php
										if($equipment['equipment_id']=="4"){
									?>
									<a title =" view Equipment (<?php echo $equipment['facility_name'];?>'s')  PIMA Details" href="javascript:void(null);" style="border-radius:1px; " class="" onclick="edit_facility(<?php echo $equipment['facility_id'];?>)"> 
										<span style="" class="glyphicon glyphicon-list-alt">
										</span>
										<?php echo $equipment['equipment'];?>
									</a>
									<?php 
										$i++;
										}
									?>
								</td>
							</tr>
							<?php
								}
							?>
						</tbody>
	            	</table>
	      		</div>		      		
	      		<div class="modal-footer" style="height:11px;padding-top:11px;">
	      			<?php echo $this->config->item("copyrights");?>
	      		</div> 
	   		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="unrecognizeddevices" >
	  	<div class="modal-dialog" style="width:50%;margin-bottom:2px;">
	    	<div class="modal-content" >
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title">Unrecognized devices that attempted uploads for <?php echo Date("Y,F");?></h4>
	      		</div>
	      		<div class="modal-body" style="padding-bottom:0px;">
	            	<table id="data-table-side2">
	            		<thead>				
							<th>#</th>
							<th>User</th>							
							<th>Equipment Type</th>				
							<th>Serial Number</th>				
							<th># Upload Attempts</th>							
						</thead>
						<tbody>
							<?php
								$i=1;
								foreach ($failed_uploads as $equipment) {
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $equipment['name'];?></td>
								<td>
									<?php
										if($equipment['equipment']=="Alere PIMA"){
									?>
									<a title ="" href="javascript:void(null);" style="border-radius:1px; " class="" onclick="edit_facility(<?php echo $equipment['equipment_id'];?>)"> 
										<span style="" class="glyphicon glyphicon-list-alt">
										</span>
										<?php echo $equipment['equipment'];?>
									</a>
									<?php 
										$i++;
										}
									?>
								</td>
								<td><?php echo $equipment['serial_num'];?></td>
								<td><?php echo $equipment['attempts'];?></td>
							</tr>
							<?php
								}
							?>
						</tbody>
	            	</table>
	      		</div>		      		
	      		<div class="modal-footer" style="height:11px;padding-top:11px;">
	      			<?php echo $this->config->item("copyrights");?>
	      		</div> 
	   		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="modal fade" id="requestedfacilitiees" >
	  	<div class="modal-dialog" style="width:50%;margin-bottom:2px;">
	    	<div class="modal-content" >
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title">Facilities Requested for registration <?php echo Date("Y,F");?></h4>
	      		</div>
	      		<div class="modal-body" style="padding-bottom:0px;">
	            	<table id="data-table-side2">
	            		<thead>	
	            		<center>
							<th rowspan="2">#</th>
							<th rowspan="2">Facility</th>							
							<th rowspan="2">Serial Number</th>				
							<th rowspan="2">Requested By</th>				
							<th rowspan="2">CTC ID No</th>	
							<th rowspan="2">Description</th>	
							<th colspan="2"><center>Action</center></th>
							<tr>
								<th><center>Approve</center></th>
								<th><center>Reject</center></th>
							</tr>
						</center>	
						</thead>
						<tbody>
							<?php
								$i=1;
								foreach ($requests as $req) {
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $req['facility_id'];?></td>
								<td><?php echo $req['serial_number'];?></td>
								<td><?php echo $req['username'];?></td>
								<td><?php echo $req['description'];?></td>
								<td><?php echo $req['ctc_id_no'];?></td>
								<td>
									<center>
										<a href="<?php echo base_url().'admin/facilities/request_responce/'.$req['id'].'/1';?>">
											<span style="font-size: 1.3em;color:#2aabd2;" class="glyphicon glyphicon-ok-sign"></span>
										</a>
									</center>
								</td>
								<td>
									<center>
										<a href="<?php echo base_url().'admin/facilities/request_responce/'.$req['id'].'/3'; ?>">
										<span style="font-size: 1.3em;color:#c12e2a;" class="glyphicon glyphicon-remove-sign"></span>
									</a>
									</center>
								</td>
							</tr>
							<?php
								$i++;}
							?>
						</tbody>
	            	</table>
	      		</div>		      		
	      		<div class="modal-footer" style="height:11px;padding-top:11px;">
	      			<?php echo $this->config->item("copyrights");?>
	      		</div> 
	   		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</div>
<script>
 	function systemUpload(){

 		$('#SystemUploads').modal('show');

 		alert("Wait while data is being fetched and uploaded! This may take a few moments.");

 		$.ajax({
          type:"POST",
          async:false,
          data:"",
            url:"<?php echo base_url()."uploads/server_upload"; ?>",  
            success:function(data) {
                           $('#modalbody').html(data);
                           $('#data-table1').dataTable({
								"bProcessing": true,
								"iDisplayLength": 5,
							 	"bJQueryUI":true,
								"bSort":false,
							  	"bPaginate":true,
							  	"bLengthChange": false,
							 	//"sScrollY": "160px",
							  	"bFilter": false
							});
              }
      });
 	}
</script>