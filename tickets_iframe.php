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

//$file = $GLOBAL['path']  . "/templates/" . $dir . "/header2.phtml";
//$template->load_template($file,$null);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ticket Pointe</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

   <style>
   #ticket_table{
	padding-top: 5px;
   }

   #check_out{
	padding-top:10px;
   }
   </style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- header with logo -->
    <section id="head">
      <div class="container">
        <div class="row">
          <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
           
            <h4>Ticket Information</h4>

          </div>
        </div>

      </div>

    </section>


<?php




if (($_GET['section'] == "") && ($_POST['section'] == "")) {
	$sql2 = "SELECT * FROM `tickets` WHERE `eventID` = '$_GET[id]'";
	$result2 = $tickets->new_mysql($sql2);
	for ($y=0; $y < 51; $y++) {
		$qty .= "<option value=\"$y\">$y</option>";
	}
	$viewID = rand(50,500);

	switch ($dir) {
	case "desktop":
	print "
	<form name=\"myform\" action=\"tickets_iframe_checkout.php\" method=\"post\" target=\"_blank\">
	<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
	<input type=\"hidden\" name=\"section\" value=\"cart\">
	<input type=\"hidden\" name=\"viewID\" value=\"$viewID\">

   <section id=\"ticket_table\">
      <div class=\"container\">
        <div class=\"row\">
          
          <div class=\"table-responsive\">          
            <table class=\"table\">
              <thead>
                <tr>
                  <th>Ticket Type</th>
                  <th>Price</th>
                  <th>Quanity</th>
                  
                </tr>
              </thead>
		<tbody>
	";

	while ($row2 = $result2->fetch_assoc()) {

                                                                // check qty
                                                                $total_avail = $row2['qty'];
                                                                $sql3 = "
                                                                SELECT
                                                                        SUM(`qty`) AS 'total'
                                                                FROM
                                                                        `cart`
                                                                WHERE
                                                                        `cart`.`ticketID` = '$row2[id]'
                                                                        AND `cart`.`status` = 'Paid'
                                                                ";
                                                                $qty_used = "0";
                                                                $result3 = $tickets->new_mysql($sql3);
                                                                while ($row3 = $result3->fetch_assoc()) {
                                                                        $qty_used = $row3['total'];
                                                                }
                                                                if ($qty_used >= $total_avail) {
                                                                        print "<tr><td colspan=3><font color=red>Sorry, <b>$row2[name]</b> is sold out.</font></td></tr>";
                                                                } else {
									print "<tr><td>$row2[name]</td><td>$".number_format($row2['price'],2,'.',',')."</td><td><select name=\"qty$row2[id]\">$qty</select></td></tr>";
								}
		$found = "1";
	}
	if ($found != "1") {
		print "<tr><td colspan=3><font color=blue>Sorry, but tickets are not yet available.</font></td></tr>";
		print "</tbody></table></div></div></div></section>";
	} else {
		print "
		</tbody></table></div></div></div></section>
		<section id=\"check_out\">
		<hr>
		";
		?>
      <div class="container">
        <div class="row">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              
              <input class="btn btn-primary btn-responsive" type="submit" value="Purchase Tickets">

              <img class="img-responsive" src="img/credit_cards.png" alt="cards">
                 
            </div>

            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
              
            </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    
                </div>
            
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <img class="img-responsive" src="img/exclusively.png" alt="logo">
                    </div>
      
      </div>



    </section>
		<?php
	}
	print "
	</form>
	";
	break;

	case "mobile":
	print '
	<style>
	.table {
	font-size: 25px
	}
	</style>
	';
        print "
        <form name=\"myform\" action=\"tickets_iframe_checkout.php\" method=\"post\" target=\"_blank\">
        <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
        <input type=\"hidden\" name=\"section\" value=\"cart\">
        <input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
        <table class=\"table\">";
        while ($row2 = $result2->fetch_assoc()) {

                                                                // check qty
                                                                $total_avail = $row2['qty'];
                                                                $sql3 = "
                                                                SELECT
                                                                        SUM(`qty`) AS 'total'
                                                                FROM
                                                                        `cart`
                                                                WHERE
                                                                        `cart`.`ticketID` = '$row2[id]'
                                                                        AND `cart`.`status` = 'Paid'
                                                                ";
                                                                $qty_used = "0";
                                                                $result3 = $tickets->new_mysql($sql3);
                                                                while ($row3 = $result3->fetch_assoc()) {
                                                                        $qty_used = $row3['total'];
                                                                }
                                                                if ($qty_used >= $total_avail) {
                                                                        print "<tr><td><font color=red>Sorry, <b>$row2[name]</b> is sold out.</font></td></tr>";
                                                                } else {
                                                                        print "<tr><td>$row2[name]</td></tr>
									<tr><td>$".number_format($row2['price'],2,'.',',')."</td></tr>
									<tr><td><select name=\"qty$row2[id]\">$qty</select></td></tr>";
                                                                }
                $found = "1";
        }
        if ($found != "1") {
                print "<tr><td><font color=blue>Sorry, but tickets are not yet available.</font></td></tr>";
        } else {
                print "<tr><td><input type=\"submit\" class=\"btn btn-primary\" value=\"Purchase Tickets\"></td></tr>";
        }
        print "</table>
        </form>
        ";


	break;
	}
}

if ($_POST['section'] == "cart") {
        $tickets->cart_iframe();
}
if ($_POST['section'] == "checkout") {
        $tickets->cart_checkout_iframe();
}
if ($_POST['section'] == "payment") {
        $tickets->payment_iframe();
}
if ($_POST['section'] == "free") {
	$tickets->free_iframe();
}
?>
