<?php
session_start();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";


$r = $_GET['r']  + 50;
$r = ($r / 2);
if ($r != $_SESSION['random']) {
	print "<br>Sorry, but the session has expired. Please close this screen then refresh the event page and click on the add to calendar button again.<br>";
	print "Test: $_SESSION[random] | $r<br>";
	die;
}

$sql = "
SELECT
	DATE_FORMAT(`events`.`start_date`, '%Y%m%d') AS 'start_date',
        DATE_FORMAT(`events`.`end_date`, '%Y%m%d') AS 'end_date',
	`events`.`start_time`,
	`events`.`end_time`,
	`events`.`homepage`,
	`events`.`title`,
	`events`.`description`

FROM
	`events`

WHERE
	`events`.`id` = '$_GET[id]'

";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
	$ampm = substr($row['start_time'], -2);
	if ($ampm == "am") {
		$startTime = "0" . $row['start_time'];
		$startTime = substr($startTime,0, -2);
		$startTime = str_replace(":","",$startTime);
	}
	if ($ampm == "pm") {
		$t1 = substr($row['start_time'],0,2);
		$t2 = substr($row['start_time'],2,2);
		$t1 = $t1 + 12;
		$startTime = $t1 . $t2;
	}	

        $ampm = substr($row['end_time'], -2);
        if ($ampm == "am") {
                $endTime = "0" . $row['end_time'];
                $endTime = substr($endTime,0, -2);
                $endTime = str_replace(":","",$endTime);
        }
        if ($ampm == "pm") {
                $t1 = substr($row['end_time'],0,2);
                $t2 = substr($row['end_time'],2,2);
                $t1 = $t1 + 12;
                $endTime = $t1 . $t2;
        }

	$date = $row['start_date'];
	$date2 = $row['end_date'];
	$subject = $row['title'];
	$desc = strip_tags($row['description']);
}

    //$date      = $_GET['date'];
    //$date = "20151028";
    //$date2 = "20151031";
	
    //$startTime = $_GET['startTime'];
    //$startTime = "1300";

    //$endTime   = $_GET['endTime'];
    //$endTime = "1900";

    //$subject   = $_GET['subject'];
    //$subject = "Test 1";

    //$desc      = $_GET['desc'];
    //$desc = "Test 2";

    $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)) . "example.com
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".$date."T".$startTime."00
DTEND:".$date2."T".$endTime."00
SUMMARY:".$subject."
DESCRIPTION:".$desc."
END:VEVENT
END:VCALENDAR";

    //set correct content-type-header
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo $ical;
?>
