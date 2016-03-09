<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

if ($_GET['email'] == "") {
        print "<font color=red>Not available</font>";
	die;
}

$sql = "SELECT `email` FROM `users` WHERE `email` = '$_GET[email]'";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
        $found = "1";
}
if ($found == "1") {
        print "<font color=red>Not available</font>";
	die;
}

if (filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        print "<i class=\"fa fa-check\" style=\"color:green\"></i>";
} else {
	print "<font color=red>Invalid</font>";
}
?>
