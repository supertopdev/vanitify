<?php 
session_start();
include(dirname(__FILE__)."/constants.php"); 
include(dirname(__FILE__)."/classes/class_connection.php");

$obj_database = new saasappoint_database();
$conn = $obj_database->connect();
$obj_database->check_superadmin_setup_detail($conn);
$obj_database->saasappoint_version_update($conn);

/** Check business id is set or not **/
if(isset($_GET['bid'])){
	$bid = base64_decode($_GET['bid']);
	if(is_numeric($bid)){
		$_SESSION['business_id'] = $bid;
		header("location:".SITE_URL); 
		exit;
	}
}

if(!isset($_SESSION['business_id'])){
	header("location:".SITE_URL."business-directory.php");exit;
}else if(!is_numeric($_SESSION["business_id"])){
	header("location:".SITE_URL."business-directory.php");exit;
}
$show_location_selector = "N";
if(!isset($_SESSION["saasappoint_location_selector_zipcode"])){
	/* header("location:".SITE_URL."location-selector.php");exit; */
	$show_location_selector = "Y";
}else if($_SESSION["saasappoint_location_selector_zipcode"] == ""){
	/* header("location:".SITE_URL."location-selector.php");exit; */
	$show_location_selector = "Y";
}

$obj_database->check_business_status($_SESSION['business_id'], $conn);

$_SESSION['saasappoint_customer_detail'] = array();
$_SESSION['saasappoint_cart_items'] = array();
$_SESSION['saasappoint_cart_category_id'] = "";
$_SESSION['saasappoint_cart_service_id'] = "";
$_SESSION['saasappoint_cart_datetime'] = "";
$_SESSION['saasappoint_cart_end_datetime'] = "";
$_SESSION['saasappoint_cart_freqdiscount_label'] = "";
$_SESSION['saasappoint_cart_freqdiscount_key'] = "";
$_SESSION['saasappoint_cart_freqdiscount_id'] = "";
$_SESSION['saasappoint_cart_subtotal'] = 0;
$_SESSION['saasappoint_cart_freqdiscount'] = 0;
$_SESSION['saasappoint_cart_coupondiscount'] = 0;
$_SESSION['saasappoint_cart_couponid'] = "";
$_SESSION['saasappoint_cart_tax'] = 0;
$_SESSION['saasappoint_cart_nettotal'] = 0;
$_SESSION['saasappoint_referral_discount_amount'] = 0;
$_SESSION['saasappoint_applied_ref_customer_id'] = "";
$_SESSION['saasappoint_ref_customer_id'] = "";

/* Include class files */

include(dirname(__FILE__)."/classes/class_frontend.php");
include(dirname(__FILE__)."/classes/class_settings.php");

/* Create object of classes */

$obj_frontend = new saasappoint_frontend();
$obj_frontend->conn = $conn; 
$obj_frontend->business_id = $_SESSION['business_id']; 

$obj_settings = new saasappoint_settings();
$obj_settings->conn = $conn;
$obj_settings->business_id = $_SESSION['business_id'];

$check_expiry = $obj_settings->check_subscription_expiry();
if($check_expiry == "business_not_exist"){
	echo "<b>Check <a href='".SITE_URL."business-directory.php'>Business Directory</a> to book an appointment with active businesses.</b>";exit;
}else{
	$expiry_date = strtotime($check_expiry);
	$current_date = strtotime(date("Y-m-d H:i:s"));
	if($current_date>$expiry_date){
		echo "<b>Subscription of this business is expired. Please upgrade your subscription to use our services. <a href='".SITE_URL."backend'>Login Now</a></b>";exit;
	}
}

/* check location selector status */
$saasappoint_location_selector_status = $obj_settings->get_option("saasappoint_location_selector_status"); 
if($saasappoint_location_selector_status == "N" || $saasappoint_location_selector_status == ""){ 
	$show_location_selector = "N";
	$_SESSION['saasappoint_location_selector_zipcode'] = "N/A";
} 
if(isset($_SESSION["saasappoint_location_selector_zipcode"])){
	if($saasappoint_location_selector_status == "Y" && ($_SESSION["saasappoint_location_selector_zipcode"]=="" && $_SESSION["saasappoint_location_selector_zipcode"]!="N/A")){
		$show_location_selector = "Y";
		$_SESSION['saasappoint_location_selector_zipcode'] = "";
	}
}
/** zipcode checker **/
if(isset($_SESSION["saasappoint_location_selector_zipcode"])){
	if($_SESSION['saasappoint_location_selector_zipcode'] != "N/A"){
		$selector_zipcode = $_SESSION["saasappoint_location_selector_zipcode"];
		$saasappoint_location_selector = $obj_settings->get_option('saasappoint_location_selector');
		$exploded_saasappoint_location_selector = explode(",", $saasappoint_location_selector);
		if(!in_array($selector_zipcode, $exploded_saasappoint_location_selector)){
			/* header("location:".SITE_URL."location-selector.php");exit; */
			$show_location_selector = "Y";
		}
	}
}
$all_categories = $obj_frontend->get_all_categories(); 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<?php 
		$saasappoint_seo_ga_code = $obj_settings->get_option('saasappoint_seo_ga_code');
		$saasappoint_seo_meta_tag = $obj_settings->get_option('saasappoint_seo_meta_tag');
		$saasappoint_seo_meta_description = $obj_settings->get_option('saasappoint_seo_meta_description');
		$saasappoint_seo_og_meta_tag = $obj_settings->get_option('saasappoint_seo_og_meta_tag');
		$saasappoint_seo_og_tag_type = $obj_settings->get_option('saasappoint_seo_og_tag_type');
		$saasappoint_seo_og_tag_url = $obj_settings->get_option('saasappoint_seo_og_tag_url');
		$saasappoint_seo_og_tag_image = $obj_settings->get_option('saasappoint_seo_og_tag_image'); 
		?>
		
		<title><?php if($saasappoint_seo_meta_tag != ""){ echo $saasappoint_seo_meta_tag; }else{ echo $obj_settings->get_option("saasappoint_company_name"); } ?></title>
		
		<?php 
		if($saasappoint_seo_meta_description != ''){ 
			?>
			<meta name="description" content="<?php echo $saasappoint_seo_meta_description; ?>">
			<?php 
		} 
		if($saasappoint_seo_og_meta_tag != ''){ 
			?>
			<meta property="og:title" content="<?php  echo $saasappoint_seo_og_meta_tag; ?>" />
			<?php 
		} 
		if($saasappoint_seo_og_tag_type != ''){ 
			?>
			<meta property="og:type" content="<?php echo $saasappoint_seo_og_tag_type; ?>" />
			<?php 
		} 
		if($saasappoint_seo_og_tag_url != ''){ 
			?>
			<meta property="og:url" content="<?php echo $saasappoint_seo_og_tag_url; ?>" />
			<?php 
		} 
		if($saasappoint_seo_og_tag_image != '' && file_exists("includes/images/".$saasappoint_seo_og_tag_image)){ 
			?>
			<meta property="og:image" content="<?php  echo SITE_URL; ?>includes/images/<?php echo $saasappoint_seo_og_tag_image; ?>" />
			<?php 
		} 
		if($saasappoint_seo_ga_code != ''){ 
			?>
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $saasappoint_seo_ga_code; ?>"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', '<?php echo $saasappoint_seo_ga_code; ?>');
			</script>
			<?php  
		} 
		?>
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/bootstrap.min.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/font-awesome.min.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/pe-icon-7-stroke.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/datepicker.min.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/saasappoint-front-style.css?<?php echo time(); ?>">
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/front/css/saasappoint-front-calendar-style.css?<?php echo time(); ?>">
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/vendor/sweetalert/sweetalert.css?<?php echo time(); ?>">
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>includes/vendor/intl-tel-input/css/intlTelInput.css?<?php echo time(); ?>">
		
		<!-- Bootstrap core JavaScript and Page level plugin JavaScript-->
		<script src="<?php echo SITE_URL; ?>includes/front/js/jquery-3.2.1.min.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/front/js/popper.min.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/front/js/bootstrap.min.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/front/js/slick.min.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/front/js/datepicker.min.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/sweetalert/sweetalert.js?<?php echo time(); ?>"></script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/jquery/jquery.validate.min.js?<?php echo time(); ?>"></script>
		
		<?php if($obj_settings->get_option("saasappoint_authorizenet_payment_status") == "Y" || $obj_settings->get_option("saasappoint_twocheckout_payment_status") == "Y"){ ?>
		<script src="<?php echo SITE_URL; ?>includes/vendor/jquery/jquery.payment.min.js?<?php echo time(); ?>" type="text/javascript"></script>
		<script>
		$(document).ready(function(){
			/** card payment validation **/
			$('#saasappoint-cardnumber').payment('formatCardNumber');
			$('#saasappoint-cardcvv').payment('formatCardCVC');
			$('#saasappoint-cardexmonth').payment('restrictNumeric');
			$('#saasappoint-cardexyear').payment('restrictNumeric');
		});
		</script>
		<?php } ?>
		
		<?php if($obj_settings->get_option('saasappoint_twocheckout_payment_status') == 'Y'){ ?>
		<script src="https://www.2checkout.com/checkout/api/2co.min.js" type="text/javascript"></script>	
		<?php } ?>
		
		<!-- Custom scripts -->
		<script>
			var generalObj = { 'site_url' : '<?php echo SITE_URL; ?>', 'ajax_url' : '<?php echo AJAX_URL; ?>', 'ty_link' : '<?php echo $obj_settings->get_option('saasappoint_thankyou_page_url'); ?>', 'twocheckout_status' : '<?php echo $obj_settings->get_option('saasappoint_twocheckout_payment_status'); ?>', 'twocheckout_sid' : '<?php echo $obj_settings->get_option('saasappoint_twocheckout_seller_id'); ?>', 'twocheckout_pkey' : '<?php echo $obj_settings->get_option('saasappoint_twocheckout_publishable_key'); ?>', 'stripe_status' : '<?php echo $obj_settings->get_option('saasappoint_stripe_payment_status'); ?>', 'stripe_pkey' : '<?php echo $obj_settings->get_option('saasappoint_stripe_publishable_key'); ?>', 'location_selector' : '<?php echo $show_location_selector; ?>' };
		</script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/intl-tel-input/js/intlTelInput.js?<?php echo time(); ?>"></script>
	</head>
	<body class="saasappoint">
		<header class="saasappoint-header-style">
			<div class="row">
				<div class="col-md-5 pl-5"><?php if($obj_settings->get_option("saasappoint_company_logo") != "" && file_exists("includes/images/".$obj_settings->get_option("saasappoint_company_logo"))){ ?><img class="saasappoint-companylogo" src="<?php echo SITE_URL; ?>includes/images/<?php echo $obj_settings->get_option("saasappoint_company_logo"); ?>" /> <?php }else{ ?><b class="saasappoint-companytitle"><?php echo $obj_settings->get_option("saasappoint_company_name"); ?></b><?php } ?></div>
				<div class="col-md-7 pull-right"><a href="<?php echo SITE_URL; ?>backend/my-appointments.php" class="btn btn-link">My Appointments</a> | <a href="<?php echo SITE_URL; ?>business-directory.php" class="btn btn-link">Book with another Business &raquo;</a> | <a href="<?php echo SITE_URL; ?>location-selector.php" class="btn btn-link">Book at another Location &raquo;</a></div>
			</div>
		</header>
		<section class="saasappoint-booking-detail-block saasappoint-center-block saasappoint-main-block-before">
			<div id="saasappoint-loader-overlay" class="saasappoint_main_loader saasappoint_hide_loader">
				<div id="saasappoint-loader" class="saasappoint_main_loader saasappoint_hide_loader">
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-dot"></div>
					<div class="saasappoint-loader-loading"></div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-md-8 saasappoint-set-sm-fit mb-4">
						<div class="saasappoint-booking-detail-main">
							<div class="saasappoint-radio-group-block saasappoint-company-services-blocks">
								<div class="saasappoint-radio-group-block-content saasappoint-no-border-bottom">
									<h4>What type of cleaning?</h4>
								</div>
								<?php 
								$i=0;
								$total_cat = mysqli_num_rows($all_categories);
								if($total_cat>0){
									while($category = mysqli_fetch_array($all_categories)){ 
										$i++;
										if($i==1){
											echo '<div class="row">';
										} 
										?>
										<div class="col-xs-12 col-md-4 saasappoint-sm-box">
											<div class="saasappoint-styled-radio">
												<input type="radio" id="saasappoint-categories-radio-<?php echo $category['id']; ?>" value="<?php echo $category['id']; ?>" name="saasappoint-categories-radio" class="saasappoint-categories-radio-change">
												<label for="saasappoint-categories-radio-<?php echo $category['id']; ?>"><?php echo ucwords($category['cat_name']); ?></label>
											</div>
										</div>
										<?php 
										if($i==3){
											echo "</div>";
											$i=0;
										}
										if($total_cat==$i && $i!=3){
											echo "</div>";
										}
									}
								}else{ 
									?>
									<div class="row">
										<div class="col-xs-12 col-md-12 saasappoint-sm-box">
											<label>Please configure first services from admin area</label>
										</div>
									</div>
									<?php 
								} 
								?>
							</div>
							<div class="row saasappoint_show_hide_services">
								<div class="col-md-12">
									<div class="saasappoint-radio-group-block-content saasappoint-no-border-bottom">
										<h4>Tell us about your service</h4>
									</div>
								</div>
							</div>
							<div class="saasappoint-radio-group-block saasappoint-no-border-bottom saasappoint-mb-minus2 saasappoint_show_hide_services">
								<div id="saasappoint_services_html_content" class="row">
									<!-- services will go here -->
								</div>
							</div>
							<div class="row saasappoint-mb-minus4 saasappoint_show_hide_addons">
								<div class="col-md-12">
									<div class="saasappoint-radio-group-block-content saasappoint-no-border-bottom">
										<h4>Select addons</h4>
									</div>
								</div>
							</div>
							<div id="saasappoint_multipleqty_addon_html_content" class="saasappoint_show_hide_addons">
								<!-- multipleqty addons will go here -->
							</div>
							<div class="saasappoint-radio-group-block mt-4 saasappoint_show_hide_addons">
								<div class="row">
									<div class="col-md-12">
										<ul id="saasappoint_singleqty_addon_html_content" class="saasappoint-addons-singleqty-items d-flex flex-wrap">
											<!-- singleqty addons will go here -->
										</ul>
									</div>
								</div>
							</div>
							<div class="saasappoint-radio-group-block mt-4 show_hide_frequently_discount">
								<p>How often would you like cleaning?</p>
								<div id="saasappoint_frequently_discount_content" class="show_hide_frequently_discount">
									<!-- frequently discount will go here -->
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="saasappoint-radio-group-block-content saasappoint-no-border-bottom">
										<h4>Choose your appointment slot</h4>
									</div>
								</div>
							</div>
							<div class="row pt-0">
								<div class="col-md-12">
									<div class="saasappoint-inline-calendar">
										<div class="saasappoint-inline-calendar-container saasappoint-inline-calendar-container-boxshadow">
											<center><h3>Please wait...</h3></center>
										</div>
										<div class="saasappoint-inline-calendar-container-boxshadow saasappoint_selected_slot_detail pl-5 pr-5 pb-2 pt-3 text-center">
											
										</div>
									</div>
									<input type="hidden" id="saasappoint_time_slots_selection_date" value="" />
									<input type="hidden" id="saasappoint_time_slots_selection_starttime" value="" />
									<input type="hidden" id="saasappoint_time_slots_selection_endtime" value="" />
									<input type="hidden" id="saasappoint_fdate" value="" />
									<input type="hidden" id="saasappoint_fstime" value="" />
									<input type="hidden" id="saasappoint_fetime" value="" />
								</div>
							</div>
							<?php 
							$useremail = "";
							$userpassword = "";
							$userfirstname = "";
							$userlastname = "";
							$userzip = $_SESSION['saasappoint_location_selector_zipcode'];
							$userphone = "";
							$useraddress = "";
							$usercity = "";
							$userstate = "";
							$usercountry = "";
							if(isset($_SESSION['customer_id'])){
								$obj_frontend->customer_id = $_SESSION['customer_id'];
								$customer_detail = $obj_frontend->readone_customer();
								$useremail = $customer_detail['email'];
								$userpassword = $customer_detail['password'];
								$userfirstname = ucwords($customer_detail['firstname']);
								$userlastname = ucwords($customer_detail['lastname']);
								$userzip = $_SESSION['saasappoint_location_selector_zipcode'];
								$userphone = $customer_detail['phone'];
								$useraddress = $customer_detail['address'];
								$usercity = $customer_detail['city'];
								$userstate = $customer_detail['state'];
								$usercountry = $customer_detail['country'];
							} 
							?>
							<div class="row">
								<div class="col-md-12">
									<div class="saasappoint-radio-group-block-content saasappoint-no-border-bottom">
										<h4>Personal information</h4>
										<div class="saasappoint-users-selection-div" <?php if(isset($_SESSION['customer_id'])){ echo "style='display:none;'"; } ?>>
											<?php 
											$saasappoint_show_existing_new_user_checkout = $obj_settings->get_option("saasappoint_show_existing_new_user_checkout");
											$saasappoint_show_guest_user_checkout = $obj_settings->get_option("saasappoint_show_guest_user_checkout");
											if($saasappoint_show_existing_new_user_checkout == "Y"){ 
												?>
												<input type="radio" class="saasappoint-user-selection" id="saasappoint-existing-user" name="saasappoint-user-selection" checked value="ec" />
												<label class="saasappoint-user-selection-label" for="saasappoint-existing-user">Existing Customer</label>

												<input type="radio" class="saasappoint-user-selection" id="saasappoint-new-user" name="saasappoint-user-selection" value="nc" />
												<label class="saasappoint-user-selection-label" for="saasappoint-new-user">New Customer</label>
											
											<?php 
											}
											
											if($saasappoint_show_guest_user_checkout == "Y"){ 
												?>
												<input type="radio" class="saasappoint-user-selection" id="saasappoint-guest-user" name="saasappoint-user-selection" <?php if($saasappoint_show_existing_new_user_checkout == "N"){ echo "checked"; } ?> value="gc" />
												<label class="saasappoint-user-selection-label" for="saasappoint-guest-user">Guest Customer</label>
												<?php 
											} 
											
											if($saasappoint_show_existing_new_user_checkout == "Y" || $saasappoint_show_guest_user_checkout == "Y"){ 
												?>
												<input type="radio" class="saasappoint-user-selection" id="saasappoint-user-forget-password" name="saasappoint-user-selection" value="fp" />
												<label class="saasappoint-user-selection-label" for="saasappoint-user-forget-password">Forget Password</label>
												<?php 
											} 
											?>
										</div>
										<div class="saasappoint-logout-div mt-2" <?php if(isset($_SESSION['customer_id'])){ echo "style='display:block;'"; } ?> >
											<label>You are logged in as <b class="saasappoint_loggedin_name"><?php echo $userfirstname." ".$userlastname; ?></b>. <a href="javascript:void(0)" id="saasappoint_logout_btn">Logout</a></label>
										</div>
									</div>
								</div>
							</div>
							<?php 
							if($saasappoint_show_existing_new_user_checkout == "Y"){ 
								?>
								<form method="post" name="saasappoint_login_form" id="saasappoint_login_form">
									<div class="saasappoint-radio-group-block mt24" <?php if(isset($_SESSION['customer_id'])){ echo "style='display:none;'"; } ?> id="saasappoint-existing-user-box">
										<div class="row">
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="email" name="saasappoint_login_email" id="saasappoint_login_email" placeholder="Email Address" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="password" name="saasappoint_login_password" id="saasappoint_login_password" placeholder="Password" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row mt-2">
											<div class="col-md-12">
												<button id="saasappoint_login_btn" class="btn btn-block saasappoint-block-btn" type="submit"><i class="fa fa-lock"></i> Login</button>
											</div>
										</div>
									</div>
								</form>
								<form method="post" name="saasappoint_user_detail_form" id="saasappoint_user_detail_form">
									<div class="saasappoint-radio-group-block mt24" <?php if(isset($_SESSION['customer_id'])){ echo "style='display:block;'"; } ?> id="saasappoint-new-user-box">
										<div class="row saasappoint_hide_after_login" <?php if(isset($_SESSION['customer_id'])){ echo "style='display:none;'"; } ?>>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="email" id="saasappoint_user_email" name="saasappoint_user_email" placeholder="Email Address" value="<?php echo $useremail; ?>" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="password" id="saasappoint_user_password" name="saasappoint_user_password" placeholder="Password" value="<?php echo $userpassword; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_firstname" name="saasappoint_user_firstname" placeholder="First Name" value="<?php echo $userfirstname; ?>" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_lastname" name="saasappoint_user_lastname" placeholder="Last Name" value="<?php echo $userlastname; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row">
											<?php 
											$show_zip_input = "";
											$show_phone_div = "6";
											if($saasappoint_location_selector_status == "N" || $saasappoint_location_selector_status == ""){ 
												$show_zip_input= "saasappoint_hide";
												$show_phone_div= "12";
											}
											?>
											<div class="col-md-<?php echo $show_phone_div; ?>">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_phone" name="saasappoint_user_phone" placeholder="Phone Number" value="<?php echo $userphone; ?>" class="saasappoint-input-class">
												</div>
											</div>
											
											<div class="col-md-6 <?php echo $show_zip_input; ?>">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_zip" name="saasappoint_user_zip" placeholder="Zip" disabled value="<?php echo $userzip; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_address" name="saasappoint_user_address" placeholder="Address" value="<?php echo $useraddress; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_city" name="saasappoint_user_city" placeholder="City" value="<?php echo $usercity; ?>" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_state" name="saasappoint_user_state" placeholder="State" value="<?php echo $userstate; ?>" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_user_country" name="saasappoint_user_country" placeholder="Country" value="<?php echo $usercountry; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
									</div>
								</form>
							<?php } ?>
							
							<?php if($saasappoint_show_guest_user_checkout == "Y"){ ?>
								<form method="post" name="saasappoint_guestuser_detail_form" id="saasappoint_guestuser_detail_form">
									<div class="saasappoint-radio-group-block mt24" <?php if($saasappoint_show_existing_new_user_checkout == "N"){ echo "style='display:block;'"; } ?> id="saasappoint-guest-user-box">
										<div class="row">
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_firstname" name="saasappoint_guest_firstname" placeholder="First Name" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_lastname" name="saasappoint_guest_lastname" placeholder="Last Name" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_email" name="saasappoint_guest_email" placeholder="Email Address" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_phone" name="saasappoint_guest_phone" placeholder="Phone Number" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<?php 
											$show_gzip_input = "";
											$show_gaddress_div = "9";
											if($saasappoint_location_selector_status == "N" || $saasappoint_location_selector_status == ""){ 
												$show_gzip_input= "saasappoint_hide";
												$show_gaddress_div= "12";
											}
											?>
											<div class="col-md-<?php echo $show_gaddress_div; ?>">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_address" name="saasappoint_guest_address" placeholder="Address" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-3 <?php echo $show_gzip_input; ?>">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_zip" name="saasappoint_guest_zip" placeholder="Zip" disabled value="<?php echo $userzip; ?>" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_city" name="saasappoint_guest_city" placeholder="City" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_state" name="saasappoint_guest_state" placeholder="State" class="saasappoint-input-class">
												</div>
											</div>
											<div class="col-md-4">
												<div class="saasappoint-input-class-div">
													<input type="text" id="saasappoint_guest_country" name="saasappoint_guest_country" placeholder="Country" class="saasappoint-input-class">
												</div>
											</div>
										</div>
									</div>
								</form>
							<?php } ?>
							<?php if($saasappoint_show_existing_new_user_checkout == "Y" || $saasappoint_show_guest_user_checkout == "Y"){ ?>
								<form id="saasappoint_forgot_password_form" name="saasappoint_forgot_password_form">
									<div class="saasappoint-radio-group-block mt24" id="saasappoint-user-forget-password-box">
										<div class="row">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<input type="email" id="saasappoint_forgot_password_email" name="saasappoint_forgot_password_email" placeholder="Your Registered Email Address" class="saasappoint-input-class">
													<label id="saasappoint-forgot-password-success"></label>
													<label id="saasappoint-forgot-password-error" class="error"></label>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<button class="btn btn-block saasappoint-block-btn" id="saasappoint_forgot_password_btn" type="submit"><i class="fa fa-envelope"></i> Send Mail</button>
											</div>
										</div>
									</div>
								</form>
							<?php } ?>
							<div class="row">
								<div class="col-md-12">
									<div class="saasappoint-radio-group-block-content">
										<h4>Payment method </h4>
										<div class="saasappoint-payment-icon">
											<i class="fa fa-lock" aria-hidden="true"></i>
											<p>256 bit Secure<br> SSL Encryption</p>
										</div>
										<div class="row mt-2">
											<div class="saasappoint-payments">
												<input type="radio" class="saasappoint-payment-method-check" id="saasappoint-pay-at-venue" name="saasappoint-payment-method-radio" value="pay-at-venue" checked />
												<label for="saasappoint-pay-at-venue">Pay at Venue</label>
												<?php 
												if($obj_settings->get_option("saasappoint_paypal_payment_status") == "Y"){ 
													?>
													<input type="radio" class="saasappoint-payment-method-check" id="saasappoint-paypal-payment" name="saasappoint-payment-method-radio" value="paypal" />
													<label for="saasappoint-paypal-payment">PayPal</label>
													<?php 
												} 
												if($obj_settings->get_option("saasappoint_stripe_payment_status") == "Y" && $obj_settings->get_option("saasappoint_authorizenet_payment_status") == "N" && $obj_settings->get_option("saasappoint_twocheckout_payment_status") == "N"){ 
													$payment_method = "stripe";
												} else if($obj_settings->get_option("saasappoint_stripe_payment_status") == "N" && $obj_settings->get_option("saasappoint_authorizenet_payment_status") == "Y" && $obj_settings->get_option("saasappoint_twocheckout_payment_status") == "N"){ 
													$payment_method = "authorize.net";
												}  else if($obj_settings->get_option("saasappoint_stripe_payment_status") == "N" && $obj_settings->get_option("saasappoint_authorizenet_payment_status") == "N" && $obj_settings->get_option("saasappoint_twocheckout_payment_status") == "Y"){ 
													$payment_method = "2checkout";
												} else{
													$payment_method = "N";
												}
												if($payment_method != "N"){ 
													?>
													<input type="radio" class="saasappoint-payment-method-check" id="saasappoint-card-payment" name="saasappoint-payment-method-radio" value="<?php echo $payment_method; ?>" />
													<label for="saasappoint-card-payment">Card Payment</label>
													<?php 
												} 
												?>
											</div>
											<div class="col-md-12">
												<div class="pull-left fa-border saasappoint_applied_coupon_div">
													<span class="saasappoint_applied_coupon_badge"><i class="fa fa-ticket"></i> </span>
												</div>
												<a href="javascript:void(0)" class="pull-left saasappoint_remove_applied_coupon" data-id=""><i class="fa fa-times-circle-o fa-lg"></i></a>
												<a href="javascript:void(0)" class="pull-right" id="saasappoint-available-coupons-open-modal">Check available promo</a>
											</div>
											<div class="col-md-12 text-left mt-3 saasappoint_apply_referral_coupon_div">
												<a href="javascript:void(0)" id="saasappoint_apply_referral_coupon"><span>Do you have referral discount coupon?</span></a>
											</div>
											<div class="col-md-12 text-left mt-3 saasappoint_applied_referral_coupon_div_text">
												<span>Applied referral discount coupon: <b class="saasappoint_applied_referral_coupon_code"></b></span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="saasappoint-radio-group-block">
								<div class="saasappoint-card-detail-box">
									<p>Credit Card details</p>
									<?php 
									if($obj_settings->get_option("saasappoint_stripe_payment_status") == "Y" && $obj_settings->get_option("saasappoint_authorizenet_payment_status") == "N" && $obj_settings->get_option("saasappoint_twocheckout_payment_status") == "N"){ 
										?>
										<div class="mb-4">
											<div id="saasappoint_stripe_plan_card_element">
												<!-- A Stripe Element will be inserted here. -->
											</div>
											<!-- Used to display form errors. -->
											<div id="saasappoint_stripe_plan_card_errors" role="alert"></div>
										</div>
										<?php 
									}else{ 
										?>
										<div class="row">
											<div class="col-md-9">
												<div class="saasappoint-input-class-div">
													<input maxlength="20" size="20" type="tel" placeholder="Card number" class="saasappoint-input-class saasappoint-card-num" name="saasappoint-cardnumber" id="saasappoint-cardnumber" value="" />
												</div>
											</div>
											<div class="col-md-3">
												<div class="saasappoint-input-class-div">
													<input type="password" maxlength="4" size="4" placeholder="CVV" class="saasappoint-input-class"  name="saasappoint-cardcvv" id="saasappoint-cardcvv" value="" />
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-md-3">
												<div class="saasappoint-input-class-div">
													<input maxlength="2" type="tel" placeholder="MM" class="saasappoint-input-class" name="saasappoint-cardexmonth" id="saasappoint-cardexmonth" value="" />
												</div>
											</div>
											<div class="col-md-3">
												<div class="saasappoint-input-class-div">
													<input maxlength="4" type="tel" placeholder="YYYY" class="saasappoint-input-class" name="saasappoint-cardexyear" id="saasappoint-cardexyear" value="" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="saasappoint-input-class-div">
													<input type="text" placeholder="Name as on Card" class="saasappoint-input-class" name="saasappoint-cardholdername" id="saasappoint-cardholdername" value="" />
												</div>
											</div>
										</div>
										<?php 
									} 
									?>
								</div>
								<div class="saasappoint-radio-group-block mt-4 mb-5 saasappoint_referral_code_applied_div">
									<div class="row">
										<div class="col-md-12">
											<span>Applied referral code: <b class="saasappoint_referral_code_applied_text"></b></span>
										</div>
									</div>
								</div>
								<div class="saasappoint-radio-group-block mt-4 mb-5 saasappoint_referral_code_div">
									<div class="row">
										<div class="col-md-12">
											<span>Do you have referral code?</span>
										</div>
									</div>
									<div class="row mt-2 ml-1">
										<div class="col-md-5 pl-0 pr-0">
											<input type="text" id="saasappoint_referral_code" name="saasappoint_referral_code" placeholder="Enter your referral code" minlength="15" maxlength="15" class="form-control rounded-0 text-uppercase">
										</div>
										<div class="col-md-2 pl-0 pr-0">
											<button class="btn btn-block saasappoint-block-btn rounded-0" id="saasappoint_apply_referral_code_btn" type="submit">Apply</button>
										</div>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-md-12">
										<div class="saasappoint-terms-and-condition">
											<label class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input saasappoint-tc-control-input">
												<span class="custom-control-indicator saasappoint-tc-control-indicator"></span>
												<span class="custom-control-description saasappoint-tc-control-description">I read and agree to the <a target="_blank" href="<?php $saasappoint_terms_and_condition_link = $obj_settings->get_option("saasappoint_terms_and_condition_link"); if($saasappoint_terms_and_condition_link != ""){ echo $saasappoint_terms_and_condition_link; }else{ echo "javascript:void(0)"; } ?>">terms & conditions</a></span>
											</label>
										</div>
									</div>
								</div>
								<div class="row mt-4">
									<div class="col-md-12">
										<button id="saasappoint_book_appointment_btn" class="btn btn-block saasappoint-big-block-btn" type="submit"><span class="fa fa-calendar-check-o"></span>Book Now</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 saasappoint-set-sm-fit mb-5">
						<div class="saasappoint_sticky_bottom_booking_summary">
							<div class="saasappoint-sidebar-block-title">
								<h4>Booking Summary</h4>
							</div>
							<div id="saasappoint_refresh_cart" class="saasappoint-sidebar-block-content">
								<label>No items in cart</label>
							</div>
						</div>
						<?php 
						if($obj_settings->get_option("saasappoint_show_frontend_rightside_feedback_form") == "Y"){ 
							?>
							<div class="mt-3">
								<div class="saasappoint-sidebar-block-title">
									<h4>Give us feedback</h4>
								</div>
								<form method="post" name="saasappoint_feedback_form" id="saasappoint_feedback_form">
									<input type="hidden" id="saasappoint_fb_rating" name="saasappoint_fb_rating" value="0" />
									<div class="saasappoint-sidebar-block-content">
										<div class="row">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<center>
														<span class="fa fa-star-o saasappoint-sidebar-feedback-star" id="saasappoint-sidebar-feedback-star1" onclick="saasappoint_add_star_rating(this,1)"></span>
														<span class="fa fa-star-o saasappoint-sidebar-feedback-star" id="saasappoint-sidebar-feedback-star2" onclick="saasappoint_add_star_rating(this,2)"></span>
														<span class="fa fa-star-o saasappoint-sidebar-feedback-star" id="saasappoint-sidebar-feedback-star3" onclick="saasappoint_add_star_rating(this,3)"></span>
														<span class="fa fa-star-o saasappoint-sidebar-feedback-star" id="saasappoint-sidebar-feedback-star4" onclick="saasappoint_add_star_rating(this,4)"></span>
														<span class="fa fa-star-o saasappoint-sidebar-feedback-star" id="saasappoint-sidebar-feedback-star5" onclick="saasappoint_add_star_rating(this,5)"></span>
													</center>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<input type="text" placeholder="Your Name" id="saasappoint_fb_name" name="saasappoint_fb_name" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<input type="email" placeholder="Email Address" id="saasappoint_fb_email" name="saasappoint_fb_email" class="saasappoint-input-class">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="saasappoint-input-class-div">
													<textarea placeholder="Your Review" id="saasappoint_fb_review" name="saasappoint_fb_review" class="saasappoint-input-class"></textarea>
												</div>
											</div>
										</div>
										<h4><button class="btn btn-block saasappoint-big-block-btn" id="saasappoint_submit_feedback_btn" type="submit"><i class="fa fa-thumbs-up"></i> Submit Review</button></h4>
									</div>
								</form>
							</div>
							<?php 
						}
						
						if($obj_settings->get_option("saasappoint_show_frontend_rightside_feedback_list") == "Y"){
							$all_feedbacks = $obj_frontend->get_all_feedbacks();
							$total_feedbacks = mysqli_num_rows($all_feedbacks);
							if($total_feedbacks>0){ 
								?>
								<div class="mt-3">
									<div class="saasappoint-sidebar-block-title">
										<h4>Our happy customers</h4>
									</div>
									<div class="saasappoint-sidebar-block-content">
										<?php 
										$fb_i=1;
										while($feedback = mysqli_fetch_array($all_feedbacks)){ 
											if($fb_i == 1){
												echo '<div class="saasappoint_list_of_feedbacks">';
											}
											?>
											<div class="row card">
												<div class="card-body">
													<h3 class="card-title"><?php echo ucwords($feedback['name']); ?></h3>
													<p class="card-text">
														<?php 
														if($feedback['rating']>0){
															for($star_i=0;$star_i<$feedback['rating'];$star_i++){ 
																?>
																<i class="fa fa-star" aria-hidden="true"></i>
																<?php 
															} 
															for($star_j=0;$star_j<(5-$feedback['rating']);$star_j++){ 
																?>
																<i class="fa fa-star-o" aria-hidden="true"></i>
																<?php 
															} 
														}else{ 
															?>
															<i class="fa fa-star-o" aria-hidden="true"></i>
															<i class="fa fa-star-o" aria-hidden="true"></i>
															<i class="fa fa-star-o" aria-hidden="true"></i>
															<i class="fa fa-star-o" aria-hidden="true"></i>
															<i class="fa fa-star-o" aria-hidden="true"></i>
															<?php 
														} 
														?>
													</p>
													<p class="card-text"><?php echo ucfirst($feedback['review']); ?></p>
												</div>
											</div>
											<?php 
											if($fb_i == 3){
												echo '</div>';
												$fb_i = 0;
											}
											if($fb_i == $total_feedbacks){
												echo '</div>';
											}
											$fb_i++;
										} 
										?>
									</div>
								</div>
								<?php 
							}  
						}  
						?>
					</div>
				</div>
			</div>
		</section>
		<!-- Available Coupon Offers START -->
		<div class="modal" id="saasappoint-available-coupons-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<!-- Modal Header -->
					<div class="modal-header">
						<h4 class="modal-title">Select a Promo Offer</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<!-- Modal body -->
					<div class="modal-body saasappoint_avail_promo_modal_body">
						
					</div>
					<!-- Modal footer -->
					<div class="modal-footer">
						
					</div>
				</div>
			</div>
		</div>
		<!-- Available Coupon Offers END -->		
		
		<!-- Location Selector Modal START -->
		<div class="modal" id="saasappoint-location-selector-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content saasappoint-location-selector-bg">
					<!-- Modal body -->
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="border-0 saasappoint-location-selector-content-box">
									<div class="col-md-12">
										<div class="border-0 pb-5 pt-3 text-center">
											<h3 class="text-white">Check for services available at your location</h3>
										</div>
									</div>
									<div id="saasappoint_location_selector_form">
										<div class="pb-3">
											<div class="row">
												<div class="col-md-12">
													<center>
														<!-- Search form -->
														<div class="card card-sm">
															<div class="card-body row no-gutters align-items-center">
																<!--end of col-->
																<div class="col">
																	<input id="saasappoint_ls_input_keyword" class="form-control form-control-lg saasappoint-form-control-borderless" type="text" placeholder="Enter zip" autocomplete="off" />
																</div>
																<!--end of col-->
																<div class="col-auto">
																	<button id="saasappoint_location_check_btn" class="btn saasappoint-block-btn pl-3 pr-3" type="submit"><i class="fa fa-map-marker"></i></button>
																</div>
																<!--end of col-->
															</div>
														</div>
													</center>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal footer
					<div class="modal-footer">
						
					</div> -->
				</div>
			</div>
		</div>
		<!-- Location Selector Modal END -->
		
		<?php if($obj_settings->get_option('saasappoint_stripe_payment_status') == 'Y'){ ?>
		<script src="https://js.stripe.com/v3/"></script>
		<?php } ?>
		<script src="<?php echo SITE_URL; ?>includes/front/js/saasappoint-front-jquery.js?<?php echo time(); ?>"></script>
	</body>
</html>