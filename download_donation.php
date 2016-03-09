<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

if ($_SESSION['id'] != "") {
	$sql = "
	SELECT
		`donate`.*,
		`events`.`title`

	FROM
		`events`,`donate`
	WHERE
		`events`.`id` = '$_GET[id]'
		AND `events`.`userID` = '$_SESSION[id]'
		AND `donate`.`eventID` = '$_GET[id]'
		AND `donate`.`status` = 'Paid'
	";
	$html = "
Order #,Description,Amount,Date Ordered,Name,Email,City,State\n";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {

		$html .= "$row[viewID]-$row[eventID],$row[title],$$row[price],$row[date],$row[name],$row[email],$row[city],$row[state]\n";
		$found = "1";
	}
	if ($row['found'] == "1") {
		$html .= "Sorry, no donations are available.\n";
	}

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=donations.csv');
	print "$html";

} else {
	print "<br><font color=red>Your session has expired. Please log back in.</font><br>";
}

?>
