<?php 
session_start();

/* Include class files */
include(dirname(dirname(dirname(__FILE__)))."/constants.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_connection.php");
include(dirname(dirname(dirname(__FILE__)))."/classes/class_businesses.php");

/* Create object of classes */
$obj_database = new saasappoint_database();
$conn = $obj_database->connect();

$obj_businesses = new saasappoint_businesses();
$obj_businesses->conn = $conn;

/* Update business status ajax */
if(isset($_POST['change_business_status'])){
	$obj_businesses->id = $_POST['id'];
	$obj_businesses->status = $_POST['status'];
	$updated = $obj_businesses->change_business_status();
	if($updated){
		echo "updated";
	}else{
		echo "failed";
	}
}