<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

if ($_GET['pass1'] == "") {
	print "<font color=red>Must enter password</font>";
	die;
}

if (($_GET['pass1'] != $_GET['pass2'])) {
	print "<font color=red>Does not match</font>";
	die;
}

if (($_GET['pass1'] == $_GET['pass2'])) {
        print "<i class=\"fa fa-check\" style=\"color:green\"></i>";
}
?>
