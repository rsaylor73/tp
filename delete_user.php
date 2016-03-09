<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";


$check = $admin->check_login();
if ($check == "TRUE") {

	$sql = "DELETE FROM `users` WHERE `id` = '$_GET[id]'";
	$result = $admin->new_mysql($sql);

	$sql = "DELETE FROM `tickets` WHERE `userID` = '$_GET[id]'";
        $result = $admin->new_mysql($sql);

	$sql = "DELETE FROM `events` WHERE `userID` = '$_GET[id]'";
        $result = $admin->new_mysql($sql);


}
?>
