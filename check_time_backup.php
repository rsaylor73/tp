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

    $data[] = $d;
    $data[] = $h;
    $data[] = $m;
    $data[] = $s;
    return $data;

    //return "$d Day(s) $h Hour(s) $m Minutes $s Seconds";
}



	//$_SESSION['page_eventID'] = "1";

                $sql = "
                SELECT
			DATE_FORMAT(`events`.`end_date`, '%Y-%m-%d') AS 'timestamp'
                FROM
                        `events`,`location`,`category`,`users`

                WHERE
                        `events`.`id` = '$_SESSION[page_eventID]'
                        AND `events`.`locationID` = `location`.`id`
                        AND `events`.`categoryID` = `category`.`id`
                        AND `events`.`userID` = `users`.`id`
                ";


         $result = $tickets->new_mysql($sql);
         while ($row = $result->fetch_assoc()) {
            $timestamp = $row['timestamp'];
         }

	$dateTime = new DateTime($timestamp); 
	$endTime = $dateTime->format('U'); 


   $now = date("U");
   $time_left = $endTime - $now;
   $time_left2 = secondsToTime($time_left);

	$days = $time_left2[0];
	$hours = $time_left2[1];
	$mins = $time_left2[2];
	$secs = $time_left2[3];

//print "$days $hours $mins $secs<br>";
/*
   if ($time_left < 0) {
	print "<b>End Date has expired</b>";
   } else {
	   print "<h2>Time Left: $time_left2</h1>";
   }
*/





/*


require_once "jpgraph/src/jpgraph.php";
require_once "jpgraph/src/jpgraph_canvas.php";

// Caption below the image
$txt="";

$w=550;$h=75;
$xm=20;$ym=20;
$tw=160;

$g = new CanvasGraph($w,$h);
$img = $g->img;

// Alignment for anchor points to use
$time = array($days,$hours,$mins,$secs);
$palign = array('DAYS','HOURS','MINS','SEC');

$n = count($palign);
$t = new Text($txt);

$y = $ym;
for( $i=0; $i < $n; ++$i ) {
        
    $x = $xm + $i*$tw;
    $t->SetColor('black');
    $t->SetAlign('left','top');
    $t->SetFont(FF_ARIAL,FS_NORMAL,22);
    //$t->SetBox();
    //$t->SetParagraphAlign($palign[$i]);
    $t->Stroke($img, $x,$y);

    $img->SetColor('black');
    $img->SetFont(FF_ARIAL,FS_NORMAL,22);
    $img->SetTextAlign('center','top');
    $img->StrokeText($x+0,$y+0,''.$time[$i].'');
    $img->StrokeText($x+0,$y+30,''.$palign[$i].'');
 
}

// .. and send back to browser
//$g->Stroke();

      $fileName = ".reports/$rand.png";
      $graph->img->Stream($fileName);
      $image = "<img src=\".reports/$fileName\">";


*/

?>
