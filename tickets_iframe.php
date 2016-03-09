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

$file = $GLOBAL['path']  . "/templates/" . $dir . "/header2.phtml";
$template->load_template($file,$null);

print "
        <div id=\"cart_iframe\">
	<div id=\"cart_iframe_inner\">
        <img src=\"img/logo80.png\"><br><br>";


if (($_GET['section'] == "") && ($_POST['section'] == "")) {
	$sql2 = "SELECT * FROM `tickets` WHERE `eventID` = '$_GET[id]'";
	$result2 = $tickets->new_mysql($sql2);
	for ($y=0; $y < 51; $y++) {
		$qty .= "<option value=\"$y\">$y</option>";
	}
	$viewID = rand(50,500);
	print "
	<form name=\"myform\" action=\"tickets_iframe.php\" method=\"post\">
	<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
	<input type=\"hidden\" name=\"section\" value=\"cart\">
	<input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
	<table class=\"table\">
	<tr><td><b>Ticket:</b></td><td><b>Price:</b></td><td><b>Quantity:</b></td></tr>";
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
	} else {
		print "<tr><td colspan=3><input type=\"submit\" class=\"btn btn-primary\" value=\"Purchase Tickets\"></td></tr>";
	}
	print "</table>
	</form>
	";
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
print "</div></div>";
?>
