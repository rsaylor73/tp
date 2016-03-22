<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

// check login TBD


$sql = "INSERT INTO `tickets` (`eventID`,`userID`,`name`,`qty`,`price`,`more_info`) VALUES ('$_GET[id]','$_SESSION[id]','$_GET[name]','$_GET[qty]','$_GET[price]','$_GET[more_info]')";
$result = $tickets->new_mysql($sql);

$sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_GET[id]' AND `userID` = '$_SESSION[id]' ORDER BY `name` ASC";
$result = $tickets->new_mysql($sql);

$device = $tickets->device_type();

if ($device == "0") {
print "
                <table class=\"table\">
                <tr><td><b>Name</b></td><td><b>Quantity</b></td><td><b>Price</b></td><td>&nbsp;</td></tr>
";
} else {
print "
                <table class=\"table\">
                <tr><td><b>Name</b></td><td><b>Quantity</b></td><td><b>Price</b></td></tr>
";	
}
while ($row = $result->fetch_assoc()) {
	if (strlen($row['name']) > 20) {
		$row['name'] = substr($row['name'], 0,20);
		$row['name'] .= " ...";
	}
	print "<tr><td>$row[name]</td><td>$row[qty]</td><td>$row[price]</td>";
	if ($device == "0") {
		print "
		<td>
        <input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='index.php?section=dashboard&center=edit_tickets&id=$_GET[id]&item=$row[id]'\">
        <input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[name]')){document.location.href='index.php?section=dashboard&center=manage_tickets&id=$_GET[id]&delete=y&item=$row[id]'};\">

		</td></tr>";
	} else {
		print "
		</tr><tr><td colspan=3>
        <input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='index.php?section=dashboard&center=edit_tickets&id=$_GET[id]&item=$row[id]'\">
        <input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[name]')){document.location.href='index.php?section=dashboard&center=manage_tickets&id=$_GET[id]&delete=y&item=$row[id]'};\">

		</td></tr>";	
	}
	$found = "1";
}
if ($found != "1") {
	print "<tr><td colspan=4>No results</td></tr>";
}
print "</table>";
