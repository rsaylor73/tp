<?php
session_start();

?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">


    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="HandheldFriendly" content="true">

    <title>Ticket Pointe | We get the pointe, Tickets!</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/desktop.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#E5E5E5">
<?php


// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

$qr = explode("-",$_GET['qr']);


if ($_GET['section'] == "") {
	$settings = $tickets->get_settings();
	$sql = "
	SELECT 
		`events`.`title`,
		`events`.`userID`,
		`events`.`id`,
		`cart`.`description`,
		`cart`.`qty`,
		DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
	        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
		`events`.`cover_image`

	FROM 
		`cart`,`events`

	WHERE 
		`cart`.`id` = '$qr[0]' 
		AND `cart`.`sessionID` = '$qr[1]' 
		AND `cart`.`viewID` = '$qr[2]'
		AND `cart`.`eventID` = `events`.`id`
	";

	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		print "
		<font size=24>
		<blockquote>
		<center>
			<br><br>
			<img src=\"$settings[1]uploads/$row[userID]/cover/$row[id]/$row[cover_image]\" width=\"200\" height=\"200\">
		</center>
		<br><br>
		<b>Event: $row[title]</b><br>
		<b>Valid $row[start_date] to $row[end_date]</b><br>
		<b>Ticket: $row[description]</b><br>
		<b>Quantity: $row[qty]</b><br><br>

		<center><input type=\"button\" class=\"btn btn-primary\" value=\"Check In\" onclick=\"document.location.href='readqr.php?section=validate&qr=$_GET[qr]'\"></center><br><br>

		</blockquote>
		</font>
		";
	}
}

if ($_GET['section'] == "validate") {
        $sql = "
        SELECT
                `events`.`title`,
                `cart`.`description`,
                `cart`.`qty`,
                DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
		`cart`.`id`,
		`cart`.`consumed`

        FROM
                `cart`,`events`

        WHERE
                `cart`.`id` = '$qr[0]'
                AND `cart`.`sessionID` = '$qr[1]'
                AND `cart`.`viewID` = '$qr[2]'
                AND `cart`.`eventID` = `events`.`id`
		AND `cart`.`consumed` = 'No'
        ";
        $result = $tickets->new_mysql($sql);
        while ($row = $result->fetch_assoc()) {
		$found = "1";
		if ($row['consumed'] == "No") {
	                print "
        	        <font size=24>
                	<blockquote>
	                <br><br>
        	        <b>Event: $row[title]</b><br>
                	<b>Valid $row[start_date] to $row[end_date]</b><br>
	                <b>Ticket: $row[description]</b><br>
        	        <b>Quantity: $row[qty]</b><br>
			<b><font color=blue>Ticket is now consumed. Person may enter event.</font></b><br>

	                </blockquote>
        	        </font>
			";

			$sql2 = "UPDATE `cart` SET `consumed` = 'Yes' WHERE `id` = '$row[id]'";
			$result2 = $tickets->new_mysql($sql2);
			
		}
		if ($row['consumed'] == "Yes") {
                        print "
                        <font size=24>
                        <blockquote>
                        <br><br>
			<font color=red>Sorry, this ticket was already consumed.</font><br>
                        <b>Event: $row[title]</b><br>
                        <b>Valid $row[start_date] to $row[end_date]</b><br>
                        <b>Ticket: $row[description]</b><br>
                        <b>Quantity: $row[qty]</b><br>
                        </blockquote>
                        </font>
                        ";
		}
	}
	if ($found != "1") {
                        print "
                        <font size=24>
                        <blockquote>
                        <br><br>
			<font color=red>This ticket was already used and may not be used again.</font>
                        </blockquote>
                        </font>
                        ";
	}
}

?>
