<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

if ($_SERVER['HTTP_HOST'] != $GLOBAL['domain']) {
        $sub = explode(".",$_SERVER['HTTP_HOST']);
        $redirect = "http://".$GLOBAL['domain']."/".$sub[0];
        header("Location: $redirect");
}

// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}

$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
$template->load_template($file,$null);


        print "<div id=\"page_view\">";
	/*
	print "<pre>";
	print_r($_POST);
	print_r($_SESSION);
	print "</pre>";
	*/
	$total = $_SESSION['amount'];
	$tickets->donate_payment($total);

	print "</div>";
?>
