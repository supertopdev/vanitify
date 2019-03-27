<?php 
error_reporting(E_ALL);
include("config.php");
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
    $protocol = 'https';
} else { 
    $protocol = 'http';
}
$cur_dirname = basename(__DIR__);
if($cur_dirname=='public_html'){
	$cur_dirname='';
}
if($cur_dirname != ""){
	$cur_dir = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], $cur_dirname)).$cur_dirname."/";
}else{
	$cur_dir = $cur_dirname."/";
}
$dots = explode(".",$_SERVER['HTTP_HOST']);

define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] .$cur_dir);
define("BASE_URL", substr($cur_dir,0,-1));
define("SITE_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir);
define("AJAX_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir.'includes/lib/');

?>