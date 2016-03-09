<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

if ($_SESSION['id'] != "") {
	// header
        $sql = "SELECT * FROM `registration` WHERE `eventID` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		$found = "1";
		foreach ($row as $key=>$value) {
			if (($key != "") && ($key != "eventID") && ($key != "userID") && ($key != "id")) {
				$html .= "$value,";
			}
		}
	}
	if ($found != "1") {
		die;
	}
	$html = substr($html,0,-1);
	$html .= "\n";
	// end header

	$sql = "SELECT * FROM `registration_answers` WHERE `eventID` = '$_GET[id]'";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		foreach ($row as $key=>$value) {
			if (($key != "") && ($key != "eventID") && ($key != "id")) {
				$html2 .= "$value,";
			}
		}
	        $html2 = substr($html2,0,-1);
        	$html2 .= "\n";

	}


        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=registration.csv');
        print "$html $html2";

} else {
        print "<br><font color=red>Your session has expired. Please log back in.</font><br>";
}

?>

