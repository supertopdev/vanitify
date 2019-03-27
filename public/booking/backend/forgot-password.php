<?php 
session_start();
/* Include class files */
include(dirname(dirname(__FILE__))."/constants.php");

/* Redirect if user logged in */
if(isset($_SESSION['login_type'])) { 
	?>
	<script>
	window.location.href = "<?php echo SITE_URL; ?>backend/";
	</script>
	<?php 
	exit;
}

include(dirname(dirname(__FILE__))."/classes/class_connection.php"); 
include(dirname(dirname(__FILE__))."/classes/class_settings.php");

/* Create object of classes */
$obj_database = new saasappoint_database();
$conn = $obj_database->connect();
$obj_database->check_superadmin_setup_detail($conn);
$obj_database->saasappoint_version_update($conn);

$obj_settings = new saasappoint_settings();
$obj_settings->conn = $conn;

$company_name = $obj_settings->get_superadmin_option("saasappoint_company_name");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php 
		$saasappoint_seo_ga_code = $obj_settings->get_superadmin_option('saasappoint_seo_ga_code');
		$saasappoint_seo_meta_tag = $obj_settings->get_superadmin_option('saasappoint_seo_meta_tag');
		$saasappoint_seo_meta_description = $obj_settings->get_superadmin_option('saasappoint_seo_meta_description');
		$saasappoint_seo_og_meta_tag = $obj_settings->get_superadmin_option('saasappoint_seo_og_meta_tag');
		$saasappoint_seo_og_tag_type = $obj_settings->get_superadmin_option('saasappoint_seo_og_tag_type');
		$saasappoint_seo_og_tag_url = $obj_settings->get_superadmin_option('saasappoint_seo_og_tag_url');
		$saasappoint_seo_og_tag_image = $obj_settings->get_superadmin_option('saasappoint_seo_og_tag_image'); 
		?>
		
		<title><?php if($saasappoint_seo_meta_tag != ""){ echo $saasappoint_seo_meta_tag; }else{ echo $obj_settings->get_superadmin_option("saasappoint_company_name"); } ?></title>
		
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
		if($saasappoint_seo_og_tag_image != '' && file_exists("../includes/images/".$saasappoint_seo_og_tag_image)){ 
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
		<!-- Bootstrap core CSS-->
		<link href="<?php echo SITE_URL; ?>includes/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom fonts for this template-->
		<link href="<?php echo SITE_URL; ?>includes/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
		<link href="<?php echo SITE_URL; ?>includes/vendor/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
		<!-- Custom styles for this template-->
		<link href="<?php echo SITE_URL; ?>includes/css/saasappoint-login.css" rel="stylesheet">
	</head>
	<body class="saasappoint">
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
		<section class="saasappoint-login-main">
			<div class="container saasappoint-login-container">
				<div class="row">
					<div class="col-md-8 saasappoint-login-left-block">
						<img class="d-block img-fluid saasappoint-bg-image-border" src="<?php echo SITE_URL; ?>includes/login-images/login-bg.jpg" alt="Cleaning Inc.">
						<div class="saasappoint-banner-text-top-left">
							<h2><?php echo ucwords($company_name); ?></h2>
						</div>
					</div>
					<div class="col-md-4 saasappoint-login-right-block">
						<h2 class="text-center">Forgot password?</h2>
						<form id="saasappoint_forgot_password_form" name="saasappoint_forgot_password_form">
							<div class="form-group">
								<label for="saasappoint_forgot_password_email">Enter your email address to reset your password.</label>
								<input type="text" class="form-control" id="saasappoint_forgot_password_email" name="saasappoint_forgot_password_email" placeholder="Enter registered email" />
								<label id="saasappoint-forgot-password-success" class="text-success"></label>
								<label id="saasappoint-forgot-password-error" class="error"></label>
							</div>
							<div class="col-md-12">
								<button type="submit" id="saasappoint_forgot_password_btn" class="btn float-right col-md-12">Reset Password</button>
							</div>
						</form>
						<br/>
						<br/>
						<p class="text-center mt-4"><a href="<?php echo SITE_URL; ?>backend/">Back to Login</a></a></p>
					</div>
				</div>
			</div>
		</section>
		<!-- Bootstrap core JavaScript-->
		<script src="<?php echo SITE_URL; ?>includes/vendor/jquery/jquery.min.js"></script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/jquery/jquery.validate.min.js"></script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo SITE_URL; ?>includes/vendor/sweetalert/sweetalert.js"></script>
		<!-- Custom scripts for all pages-->
		<script>
			var generalObj = { 'site_url' : '<?php echo SITE_URL; ?>', 'ajax_url' : '<?php echo AJAX_URL; ?>' };
		</script>
		<script src="<?php echo SITE_URL; ?>includes/js/saasappoint-login.js"></script>
	</body>
</html>