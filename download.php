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
		`cart`.*

	FROM
		`events`,`cart`
	WHERE
		`events`.`id` = '$_GET[id]'
		AND `events`.`userID` = '$_SESSION[id]'
		AND `cart`.`eventID` = '$_GET[id]'
		AND `cart`.`status` = 'Paid'
	";
	$html = "
Order #,Description,Price,QTY,Date Ordered,Ticket Used,Name,Email,City,State\n";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {

		$html .= "$row[viewID]-$row[eventID],$row[description],$$row[price],$row[qty],$row[date],$row[consumed],$row[name],$row[email],$row[city],$row[state]\n";
		$found = "1";
	}
	if ($row['found'] == "1") {
		$html .= "Sorry, no tickets are available.\n";
	}

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tickets.csv');
	print "$html";

} else {
	print "<br><font color=red>Your session has expired. Please log back in.</font><br>";
}

?>
