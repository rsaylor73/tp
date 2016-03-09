<?php
session_start();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";


$sql = "SELECT * FROM `admin_users` WHERE `uuname` = '$_GET[uuname]' AND `uupass` = '$_GET[uupass]' AND `status` = 'Active'";
$result = $admin->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
	$_SESSION['admin_uuname'] = $row['uuname'];
	$_SESSION['admin_uupass'] = $row['uupass'];
        $found = "1";
}


if ($found == "1") {
        $settings = $tickets->get_settings();
        print "You have been logged in. Click <a href=\"$settings[1]admin.php?action=dashboard\">here</a> to continue.<br>";
} else {
        $admin->login('<font color=red>The username and or password was incorrect.</font>');
}

?>
