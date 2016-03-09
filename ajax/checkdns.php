<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

// check login TBD


$sql = "SELECT * FROM `parked_domains` WHERE `parked_domain` = '$_GET[homepage]' LIMIT 1";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
	print "<font color=blue>This domain name appears to be in use. If this name is currently already set for your event this you can ignore this warning. But if you just typed in this homepage and you are receiving this error then the name you wish to use for your homepage is already in use.</font>";
}
?>
