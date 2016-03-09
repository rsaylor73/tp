<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

if ($_GET['uuname'] == "") {
        print "<font color=red>Not available</font>";
        die;
}


$sql = "SELECT `uuname` FROM `users` WHERE `uuname` = '$_GET[uuname]'";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
        $found = "1";
}
if ($found == "1") {
	$error .= "<li><font color=red>The username you selected is already being used.</font></li>";
}


$sql = "SELECT `email` FROM `users` WHERE `email` = '$_GET[email]'";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
        $found2 = "1";
}
if ($found2 == "1") {
        $error .= "<li><font color=red>The email you selected is already being used.</font></li>";
}

if (filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
	//
} else {
	$error .= "<li><font color=red>The email you entered is not valid.</font></li>";
}

if (($_GET['pass1'] == "") or ($_GET['pass2'] == "")) {
	$error .= "<li><font color=red>The passwords was blank.</font></li>";
}

if ($_GET['pass1'] != $_GET['pass2']) {
	$error .= "<li><font color=red>The passwords did not match.</font></li>";
}

if ($_GET['terms'] != "checked") {
	$error .= "<li><font color=red>The terms and conditions was not checked.</font></li>";
}

require_once "../recaptchalib.php";
// Register API keys at https://www.google.com/recaptcha/admin

$sitekey = "6LcnkA4TAAAAAD04ISkLGW_X0GWQnkbAUflFkI3E";
$secret = "6LcnkA4TAAAAAINvZHGtuE2Y3IJbB2oYFTIB9CG_";

// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
$lang = "en";
// The response from reCAPTCHA
$resp = null;
// The error code from reCAPTCHA, if any
$error = null;
$reCaptcha = new ReCaptcha($secret);
// Was there a reCAPTCHA response?
if ($_GET["g-recaptcha-response"]) {
        $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$_POST["g-recaptcha-response"]);



} else {
        $error .= "<li><font color=red>Sorry, the google reCAPTCHA was invalid. Click F5 then click register to try again.</font></li>";
}

if ($error != "") {
	print "$error";
} else {

	$sql = "INSERT INTO `users` (`uuname`,`uupass`,`email`,`active`,`verified`,`account_type`) VALUES ('$_GET[uuname]','$_GET[pass1]','$_GET[email]','Yes','Yes','$_GET[account_type]')";
	$result = $tickets->new_mysql($sql);
	if ($result == "TRUE") {
		print "<br><br>&nbsp;&nbsp;<font color=green>Your account was created sucessfully. You may now log in.</font><br><br>";
	} else {
		print "<br><br>&nbsp;&nbsp;<font color=red>There was an error creating your account. Most errors are due to spaces and symbols. Please try again without using any spaces or symbols.</font><br><br>";
	}

}


?>
