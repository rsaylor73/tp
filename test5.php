<?php
include "include/settings.php";
if ($_SERVER['HTTP_HOST'] != $GLOBAL['domain']) {
	$sub = explode(".",$_SERVER['HTTP_HOST']);
	$redirect = "http://".$GLOBAL['domain']."/".$sub[0];
	header("Location: $redirect");
}
?>
