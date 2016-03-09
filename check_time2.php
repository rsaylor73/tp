<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

function ensure2Digit($number) {
    if($number < 10) {
        //$number = '0' . $number;
    }
    return $number;
}

// Convert seconds into months, days, hours, minutes, and seconds.
function secondsToTime($ss) {
    $s = ensure2Digit($ss%60);
    $m = ensure2Digit(floor(($ss%3600)/60));
    $h = ensure2Digit(floor(($ss%86400)/3600));
    $d = ensure2Digit(floor(($ss%2592000)/86400));
    $M = ensure2Digit(floor($ss/2592000));

    //return "$M:$d:$h:$m:$s";
    return "<b>Time to complete your order: <font color=blue>$m Minutes $s Seconds</font></b>";
}



   if ($_SESSION['time1'] == $_SESSION['random']) {
	$endTime = $_SESSION['time2'];
   } else {
	$endTime = date("U") + 300;
	$_SESSION['time2'] = $endTime;
	$_SESSION['time1'] = $_SESSION['random'];
   }

   $now = date("U");
   $time_left = $endTime - $now;
   $time_left2 = secondsToTime($time_left);

   if ($time_left < 0) {
	?>
	<script>
	document.location.href='index.php?section=cancel';
	</script>
	<?php
  } else {
	   print "$time_left2";
   }
?>
