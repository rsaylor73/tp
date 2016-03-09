<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

// check login TBD

if ($_GET['type'] == "logout") {
	$tickets->signoff();
}

if ($_GET['type'] == "profile") {
	$tickets->profile();
}

if ($_GET['type'] == "checkin") {
	$tickets->checkin_users();
}

if ($_GET['type'] == "details") {
	$tickets->details();
}

if ($_GET['type'] == "new_details") {
	$tickets->new_details();
}

if ($_GET['type'] == "design") {
	$tickets->design();

}

if ($_GET['type'] == "new_design") {
	$tickets->new_design();
}

if ($_GET['type'] == "settings") {
	$tickets->event_settings();
}

if ($_GET['type'] == "users") {
	$tickets->users();
}

if ($_GET['type'] == "tickets") {
	$tickets->tickets();
}

if ($_GET['type'] == "social") {
	$tickets->social();
}

if ($_GET['type'] == "update_social") {
	$tickets->update_social();
}
?>
