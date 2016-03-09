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
	print "<font color=red>Not available</font>";
} else {
	print "<i class=\"fa fa-check\" style=\"color:green\"></i>";
}
?>
