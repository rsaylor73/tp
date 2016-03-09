<?php
session_start();
require "settings.php";
include "header.php";

$check = $tickets->check_login();
if ($check == "FALSE") {
        $tickets->login($null);
} else {
	print "Dashboard";


}



?>
