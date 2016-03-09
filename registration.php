<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

if ($_SERVER['HTTP_HOST'] != $GLOBAL['domain']) {
        $sub = explode(".",$_SERVER['HTTP_HOST']);
        $redirect = "http://".$GLOBAL['domain']."/".$sub[0];
        header("Location: $redirect");
}
        
// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}               

if ($_GET['h'] != "") {
        $h = $_GET['h'];
}
if ($_POST['h'] != "") {
        $h = $_POST['h'];
}
 
if ($h == "") {       
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
	$template->load_template($file,$null);
}


if ($h == "") {
	print "<div id=\"page_view\">";
}

$settings = $tickets->get_settings();

if ($_POST['id'] != "") {
	$id = $_POST['id'];
}
if ($_GET['id'] != "") {
	$id = $_GET['id'];
}

if (($_POST['section'] == "") && ($_GET['section'] == "")) {
	$_SESSION['stop'] = "";
	print "<h2>Registration</h2>";
	$sql = "SELECT * FROM `registration` WHERE `eventID` = '$id'";

	print "<form method=\"post\">
	<input type=\"hidden\" name=\"section\" value=\"save\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"hidden\" name=\"h\" value=\"$h\">
	<table class=\"table\">

	";

	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		foreach ($row as $key=>$value) {
			if (($value != "") && ($key != "id") && ($key != "eventID") && ($key != "userID")) {
				$key2 = str_replace("t","a",$key);
				print "<tr><td>$value</td><td><input type=\"text\" name=\"$key2\" size=20></td></tr>";
				$ok = "1";
			}
		}
	}
	if ($ok == "1") {
		print "<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Register\"></td></tr>";
	} else {
		print "<tr><td colspan=2><font color=red>Sorry, but the registration form is not active.</font></td></tr>";
	}


	print "
	</table>
	</form>";

}

if ($_POST['section'] == "save") {
	if ($_SESSION['stop'] == "") {
		
	        $sql = "SELECT * FROM `registration` WHERE `eventID` = '$_POST[id]'";
	        $result = $tickets->new_mysql($sql);
        	while ($row = $result->fetch_assoc()) {
                	foreach ($row as $key=>$value) {
                        	if (($value != "") && ($key != "id") && ($key != "eventID") && ($key != "userID")) {
                                	$key2 = str_replace("t","a",$key);
					$answer = $_POST[$key2];
					$sql_top .= "`$key2`,";
					$sql_bot .= "'$answer',";
				}
			}
		}
		$sql_top = substr($sql_top,0,-1);
		$sql_bot = substr($sql_bot,0,-1);
		$sql2 = "INSERT INTO `registration_answers` (`eventID`,$sql_top) VALUES ('$_POST[id]',$sql_bot)";
		$result2 = $tickets->new_mysql($sql2);
		print "<br><br>You have been registered for the event.<br>";
		$tickets->registration_notification($_POST['id']);
		$_SESSION['stop'] = "1";
	} else {
		print "<br><br>Sorry, but you have already registered.<br><br>";
	}
}

if ($h == "") {
	print "</div>";
}
?>
