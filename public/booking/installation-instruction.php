<?php 
include("constants.php"); 
include(dirname(__FILE__)."/classes/class_connection.php");

$obj_database = new saasappoint_database();
$obj_database->check_connection_for_installation();  
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="SaaSAppoint- Online Multi Business Appointment Scheduling & Reservation Booking Calendar --- SaaS Booking Software, Cleaner Booking, Multi Business Booking Software, Online Appointment Scheduling, Appointment Booking Calendar, Reservation System, Multi Business directory, Rendez-vous logiciel, Limpieza reserva, Saas appointment, Cleaning services business software, Scheduling SaaS, Booking Calendar, SAAS Appointment Calendar, Cleaning Appointment, Maid Booking Software">
		<meta name="author" content="SaaSAppoint - Wpminds">

		<title>SaaSAppoint Installation Instructions</title>

		<!-- Bootstrap core CSS -->
		<link href="<?php echo SITE_URL; ?>includes/vendor/bootstrap/css/bootstrap.min.css?<?php echo time(); ?>" rel="stylesheet">
		
		<!-- Custom fonts for this template -->
		<link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
		
		<!-- Custom styles for this template -->
		<link href="<?php echo SITE_URL; ?>includes/installation/css/saasappoint-installation-instruction.css?<?php echo time(); ?>" rel="stylesheet">
	</head>
	<body>
		<header class="saasappoint-main-header text-center text-white">
			<div class="saasappoint-main-header-content">
				<div class="container">
					<a class="saasappoint-logo-img" href="javascript:void(0);"><img src="<?php echo SITE_URL; ?>includes/installation/image/logo.png" alt="SaaSAppoint - Appointment Solution" /></a>
					<h2 class="saasappoint-main-header-subheading mb-0">Appointment Solution for all businesses</h2>
				</div>
			</div>
			<div class="saasappoint-bg-header-circle-1 saasappoint-bg-header-circle"></div>
			<div class="saasappoint-bg-header-circle-2 saasappoint-bg-header-circle"></div>
			<div class="saasappoint-bg-header-circle-3 saasappoint-bg-header-circle"></div>
			<div class="saasappoint-bg-header-circle-4 saasappoint-bg-header-circle"></div>
		</header>
	
		<section class="saasappoint-main-section-bg">
			<div class="container">
				<div class="row p-5">
					<div class="col-md-12">
						<center><h2>INSTALLATION INSTRUCTIONS</h2></center>
						<p class="text-right"><a href="#update_saasappoint_instructions">Want to update SaaSAppoint?</a></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="saasappoint-instruction-steps clearfix">
							<div class="panel panel-default col-sm-12 col-sm-offset-2">
								<div class="panel-body">
									<div class="row p-3">
										<iframe style="width: 100%; height: 550px;" src="https://www.youtube.com/embed/uW-Slfrppn0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="0" webkitallowfullscreen="0"></iframe>
									</div>
								</div>
							</div>
							<hr />
							<center><h5>Or follow below steps</h5></center>
							<hr />
							<div class="panel panel-default col-sm-12 col-sm-offset-2">
								<div class="panel-body">
									<div class="row p-3">
										<div class="col-md-9">
										<h3 class="saasappoint-instruction-steps-heading"> Step 1 </h3>
										* Upload the SaaSAppoint Software zip in your preferred directory and extract there.
										<br />
										* Open SaaSAppoint/config.php file and configure your HostName, UserName, Password, and Database Name.
										<br />
										* Also Import the SQL file(Given in database folder. Check image) in your database via phpmyadmin.
										</div>
										<div class="col-md-3">
											<img class="p-1" src="<?php echo SITE_URL; ?>includes/installation/image/ss1.jpg" alt="SaaSAppoint - Appointment Solution" />
										</div>
									</div>
								</div>
							</div>
							
							<div class="panel panel-default col-sm-12 col-sm-offset-2">
								<div class="panel-body">
									<div class="row p-3">
										<div class="col-md-12">
											<h3 class="saasappoint-instruction-steps-heading"> Step 2 </h3>
											<b>Check below server requirements:</b><br /><br />
											<div class="table-responsive">
												<table class="table text-center" cellspacing="2" cellpadding="10">
													<thead>
														<tr>
															<th> &nbsp; </th>
															<th> SaaSAppoint Server requirement </th>
															<th> Your server configuration </th>
															<th> Status (OK / Please configure) </th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>PHP Version</td>
															<td>5.3+</td>
															<td><span class="text-white"><strong><?php echo phpversion(); ?></strong></span></td>
															<td><span class="text-white"><strong><?php echo (phpversion() >= '5.3') ? 'OK' : 'Please configure'; ?></strong></span></td>
														</tr>
														<tr>
															<td>MySQLi </td>
															<td>On</td>
															<td>
																<span class="text-white"><strong><?php echo extension_loaded('mysqli') ? 'On' : 'Off'; ?></strong></span>
															</td>
															<td><span class="text-white"><strong><?php  echo extension_loaded('mysqli') ? 'OK' : 'Please configure'; ?></strong></span></td>
														</tr>
														
														<tr>
															<td>CURL</td>
															<td>Enable</td>
															<td>
																<span class="text-white"><strong><?php echo (extension_loaded('curl') == 'true')  ? 'Enabled' : 'Disabled'; ?></strong></span>
															</td>
															<td><span class="text-white"><strong><?php  echo (extension_loaded('curl') == 'true') ? 'OK' : 'Please configure'; ?></strong></span></td>
														</tr>
														<tr>
															<td>GD </td>
															<td>On</td>
															<td>
																<span class="text-white"><strong><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></strong></span>
															</td>
														
															<td><span class="text-white"><strong><?php  echo extension_loaded('gd') ? 'OK' : 'Please configure'; ?></strong></span></td>
														</tr>
														<tr>
															<td>Session Auto Start</td>
															<td>Off</td>
															<td>
																<span class="text-white"><strong><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></strong>
																</span>
															</td>
															<td><span class="text-white"><strong><?php  echo (!ini_get('session_auto_start')) ? 'OK' : 'Please configure'; ?></strong></span></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default col-sm-12 col-sm-offset-2">
								<div class="panel-body">
									<div class="row p-3">
										<div class="col-md-6">
											<h3 class="saasappoint-instruction-steps-heading"> Step 3 </h3>
											* Run the SaaSAppoint Software directory URL in your browser.
											<br />
											* Setup your profile & company details.
											<br />
											* Verify purchase code and ENJOY SaaSAppoint.
										</div>
										<div class="col-md-6">
											<h3 class="saasappoint-instruction-steps-heading"> Step 4 </h3>
											* Configure Business Types.
											<br />
											* Configure Subscription Plans
											<br />
											* Configure default Company settings, Payment settings, Email settings to use our fantastic services.
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="saasappoint-main-section-bg">
			<div class="container">
				<div class="row p-5">
					<div class="col-md-12">
						<a href="javascript:void(0)" id="update_saasappoint_instructions"></a>
						<center><h2>UPDATE SaaSAppoint INSTRUCTIONS</h2></center>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="saasappoint-instruction-steps clearfix">
							<div class="panel panel-default col-sm-12 col-sm-offset-2">
								<div class="panel-body">
									<div class="row p-3">
										<div class="col-md-9">
										* Upload the SaaSAppoint Software zip in your preferred directory and extract there.
										<br />
										* Open SaaSAppoint/config.php file and configure your HostName, UserName, Password, and Database Name.
										<br />
										* Go to your site and Run the SaaSAppoint Software directory URL in your browser.
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Footer -->
		<footer class="py-5 saasappoint-bg-black">
			<div class="container">
				<p class="m-0 text-center text-white">Copyright &copy; Wpminds <?php if(date("Y") == "2018"){ echo "2018"; }else{ echo "2018 - ".date("Y"); } ?></p>
			</div>
		<!-- /.container -->
		</footer>
	</body>
</html>