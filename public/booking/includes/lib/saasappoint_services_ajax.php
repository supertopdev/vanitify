<?php 
session_start();

/* Include class files */
include(dirname(dirname(dirname(__FILE__)))."/constants.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_connection.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_categories.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_services.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_addons.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_settings.php");

/* Create object of classes */
$obj_database = new saasappoint_database();
$conn = $obj_database->connect();

$obj_categories = new saasappoint_categories();
$obj_categories->conn = $conn;
$obj_categories->business_id = $_SESSION['business_id'];

$obj_services = new saasappoint_services();
$obj_services->conn = $conn;
$obj_services->business_id = $_SESSION['business_id'];

$obj_addons = new saasappoint_addons();
$obj_addons->conn = $conn;
$obj_addons->business_id = $_SESSION['business_id'];

$obj_settings = new saasappoint_settings();
$obj_settings->conn = $conn;
$obj_settings->business_id = $_SESSION['business_id'];

$image_upload_path = SITE_URL."/includes/images/";
$image_upload_abs_path = dirname(dirname(dirname(__FILE__)))."/includes/images/";

/* Refresh services ajax */
if(isset($_REQUEST['refresh_services'])){
	$obj_services->cat_id = $_REQUEST['catid'];
	$all_services = $obj_services->get_all_services_within_limit($_POST['start'],($_POST['start']+$_POST['length']), $_POST['search']['value'],$_POST['order'][0]['column'],$_POST['order'][0]['dir'],$_POST['draw']);
	$services = array();
	$services["draw"] = $_POST['draw'];
	$count_all_services = $obj_services->count_all_services($_POST['search']['value']);
	$services["recordsTotal"] = $count_all_services;
	$services["recordsFiltered"] = $count_all_services;
	$services['data'] =array();
	if(mysqli_num_rows($all_services)>0){
		$i=$_POST['start'];
		while($service = mysqli_fetch_assoc($all_services)){
			$i++;
			$service_arr = array();
			array_push($service_arr, $service['id']);
			array_push($service_arr, ucwords($service['title']));
			array_push($service_arr, ucwords($service['cat_name']));
			
			$checked = '';
			if($service['status'] == "Y"){ $checked = "checked"; }
			array_push($service_arr, '<label class="saasappoint-toggle-switch">
				  <input type="checkbox" data-id="'.$service['id'].'" class="saasappoint-toggle-switch-input saasappoint_change_service_status" '.$checked.' />
				  <span class="saasappoint-toggle-switch-slider"></span>
				</label>');
			
			$obj_addons->service_id = $service['id'];
			$total_addons = $obj_addons->count_all_addons_by_service_id();
			
			array_push($service_arr, '<a class="btn btn-secondary btn-sm saasappoint_set_serviceid" href="'.SITE_URL.'backend/addons.php" data-id="'.$service['id'].'"><i class="fa fa-fw fa-th-list"></i> Addons <span class="badge badge-light">'.$total_addons.'</span></a> &nbsp; <a class="btn btn-primary saasappoint-white btn-sm saasappoint-update-servicemodal" data-id="'.$service['id'].'"><i class="fa fa-fw fa-pencil"></i></a> &nbsp; <a class="btn btn-danger saasappoint-white btn-sm saasappoint_delete_service_btn" data-id="'.$service['id'].'"><i class="fa fa-fw fa-trash"></i></a> &nbsp; <a class="btn btn-warning btn-sm saasappoint-view-servicemodal" data-id="'.$service['id'].'"><i class="fa fa-fw fa-eye"></i></a>');
			array_push($services['data'], $service_arr);
		}
	}
	echo json_encode($services);
}

/* Add service ajax */
else if(isset($_POST['add_service'])){
	$obj_services->cat_id = $_POST['cat_id'];
	$obj_services->title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$obj_services->description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$obj_services->status = $_POST['status'];
	
	if($_POST['uploaded_file'] != ""){
		$new_filename = $_SESSION['business_id']."_".time();
		$uploaded_filename = $obj_settings->saasappoint_base64_to_jpeg($_POST['uploaded_file'], $image_upload_abs_path, $new_filename);
		$obj_services->image = $uploaded_filename;
	}else{
		$obj_services->image = "";
	}
	$added = $obj_services->add_service();
	if($added){
		echo "added";
	}else{
		echo "failed";
	}
}

/* Update service modal detail ajax */
else if(isset($_POST['update_service_modal_detail'])){
	$obj_services->id = $_POST['id'];
	$service = $obj_services->readone_service();
	?>
	<form name="saasappoint_update_service_form" id="saasappoint_update_service_form" method="post">
	  <div class="form-group">
		<label for="saasappoint_update_servicetitle">Service Title</label>
		<input class="form-control" id="saasappoint_update_servicetitle" name="saasappoint_update_servicetitle" type="text" placeholder="Enter Service Title" value="<?php echo $service['title']; ?>" />
	  </div>
	  <div class="form-group">
		<label for="saasappoint_update_servicedescription">Service Description</label>
		<textarea class="form-control" id="saasappoint_update_servicedescription" name="saasappoint_update_servicedescription" placeholder="Enter Service Description"><?php echo $service['description']; ?></textarea>
	  </div>
	  <div class="form-group">
		<label for="saasappoint_update_serviceimage">Service Image</label>
		<div class="saasappoint-image-upload">
			<div class="saasappoint-image-edit-icon">
				<input type='hidden' id="saasappoint-update-image-upload-file-hidden" name="saasappoint-update-image-upload-file-hidden" />
				<input type='file' id="saasappoint-update-image-upload-file" accept=".png, .jpg, .jpeg" />
				<label for="saasappoint-update-image-upload-file"></label>
			</div>
			<div class="saasappoint-image-preview">
				<div id="saasappoint-update-image-upload-file-preview" style="<?php $service_image = $service['image']; if($service_image != '' && file_exists("../images/".$service_image)){ echo "background-image: url(".SITE_URL."includes/images/".$service_image.");"; }else{ echo "background-image: url(".SITE_URL."includes/images/default-service.png);"; } ?>">
				</div>
			</div>
		</div>
	  </div>
	</form>
	<?php
}

/* View service modal detail ajax */
else if(isset($_POST['view_service_modal_detail'])){
	$obj_services->id = $_POST['id'];
	$service = $obj_services->readone_service();
	?>
	<div class="block">
		<div class="row ml-4 mr-4">
			<div class="span4">
				<img class="saasappoint-view-modal-image" src="<?php $service_image = $service['image']; if($service_image != '' && file_exists("../images/".$service_image)){ echo SITE_URL."includes/images/".$service_image; }else{ echo SITE_URL."includes/images/default-service.png"; } ?>"/>
				<div class="content-heading"><h3><?php echo ucwords($service['title']); ?> &nbsp </h3></div>
				<p>Status: <?php if($service['status'] == "Y"){ ?><label class="text-success">Activated</label><?php }else{ ?><label class="text-danger">Deactivated</label><?php } ?></p>
				<p><?php echo ucfirst($service['description']); ?></p>
			</div>
		</div>
	</div>
	<?php
}

/* Update service ajax */
else if(isset($_POST['update_service'])){
	$obj_services->id = $_POST['id'];
	$obj_services->title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$obj_services->description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$service = $obj_services->readone_service();
	$old_image = $service['image'];
	if($_POST['uploaded_file'] != ""){
		if($old_image != ""){
			if(file_exists("../images/".$old_image)){
				unlink("../images/".$old_image);
			}
		}
		$new_filename = $_SESSION['business_id']."_".time();
		$uploaded_filename = $obj_settings->saasappoint_base64_to_jpeg($_POST['uploaded_file'], $image_upload_abs_path, $new_filename);
		$obj_services->image = $uploaded_filename;
	}else{
		$obj_services->image = $old_image;
	}
	$updated = $obj_services->update_service();
	if($updated){
		echo "updated";
	}else{
		echo "failed";
	}
}

/* Delete service ajax */
else if(isset($_POST['delete_service'])){
	$obj_services->id = $_POST['id'];
	$check_appointments = $obj_services->check_appointments_before_delete_service();
	if($check_appointments==0){
		$service = $obj_services->readone_service();
		$old_image = $service['image'];
		if($old_image != ""){
			if(file_exists("../images/".$old_image)){
				unlink("../images/".$old_image);
			}
		}
		$deleted = $obj_services->delete_service();
		if($deleted){
			echo "deleted";
		}else{
			echo "failed";
		}
	}else{
		echo "appointments exist";
	}
}

/* Change service status ajax */
else if(isset($_POST['change_service_status'])){
	$obj_services->id = $_POST['id'];
	$obj_services->status = $_POST['status'];
	$status_changed = $obj_services->change_service_status();
	if($status_changed){
		echo "changed";
	}else{
		echo "failed";
	}
}