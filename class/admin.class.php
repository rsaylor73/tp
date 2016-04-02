<?php

if( !class_exists( 'Admin')) {
class Admin {
        public $linkID;

        function __construct($linkID){ $this->linkID = $linkID; }

        /*
        The function is set to only allow mysql calls to be driven
        from inside this class.
        */

        public function new_mysql($sql) {
                $result = $this->linkID->query($sql) or die($this->linkID->error.__LINE__);
                return $result;
        }

        // check login system
        public function check_login() {
                $sql = "SELECT `admin_users`.`id` FROM `admin_users` WHERE `admin_users`.`uuname` = '$_SESSION[admin_uuname]' AND `admin_users`.`uupass` = '$_SESSION[admin_uupass]' AND `admin_users`.`status` = 'Active'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $found = "1";
                }
                if ($found == "1") {
                        return "TRUE";
                } else {
                        return "FALSE";
                }
        }

        public function signoff() {
                session_destroy();
                print "<div id=\"dashboard_right\">";
                print "<br><br>You have been logged out. Loading...<br><br>";
                ?>
                <meta http-equiv="refresh" content="3; url=admin.php">
                <?php
                print "</div>";
        }

        // Login form
        public function login($msg) {

                if ($msg != "") {
                        print "<center><font color=red>$msg</font></center><br>";
                }

                print "
                <br>
                <div align=\"center\" id=\"login-scr\">
                <form name=\"myform\" id=\"myform\">
                <table border=0 width=700>
                <tr><td>
                        <table border=0 width=700>
                                <tr><td>Username:</td><td><input type=\"text\" name=\"uuname\" size=20></td></tr>
                                <tr><td>Password:</td><td><input type=\"password\" name=\"uupass\" onkeypress=\"if(event.keyCode==13) { login(this.form); return false;}\" size=20></td></tr>
                                <tr><td>&nbsp;</td><td><input type=\"button\" value=\"Login\" class=\"btn btn-primary\" onclick=\"login(this.form)\"></td></tr>
				";
                                print "</td></tr>
                        </table>
                </td></tr>
                </table>
                </form>
                </div>
                <br>";

                ?>
                                <script>

                                function login(myform) {
                                        $.get('adminlogin.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                          if (php_msg.substring(0,4) == "http") {
                                             $("#login-scr").html('<span class="details-description"><font color=green>Login successful. Loading please wait...</font><br></span>');
                                             setTimeout(function()
                                                {
                                                window.location.replace(php_msg)
                                                }
                                             ,2000);
                                          } else {
                                             $("#login-scr").html(php_msg);
                                          }
                                        });
                                }
                                </script>
                <?php

        }

	public function dashboard() {
		print "<h2>Admin</h2>";

		print "<ul>
		<li><a href=\"admin.php?section=users\">View/Modify Users</a></li>
		<li><a href=\"admin.php?section=admin_users\">View/Modify Admin Users</a></li>
		<li><a href=\"admin.php?section=account_types\">Account Types</a></li>
		<li><a href=\"admin.php?section=categories\">View/Modify Categories</a></li>
		<li><a href=\"admin.php?section=locations\">View/Modify Locations</a></li>
		<li><a href=\"admin.php?section=pages&id=1\">Edit Why Us Page</a></li>
                <li><a href=\"admin.php?section=pages&id=2\">Edit Terms Page</a></li>
		<li><a href=\"admin.php?section=refund\">Refund A Ticket</a></li>
		<li><a href=\"admin.php?section=balance_report\">Balance Report : Tickets</a></li>
                <li><a href=\"admin.php?section=balance_report_donations\">Balance Report : Donations</a></li>

		</ul>";
	
	}

	public function page() {

		$sql = "SELECT * FROM `pages` WHERE `id` = '$_GET[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			print "<h2>Edit $row[page_title]</h2>";

			print "
			<form action=\"admin.php\" method=\"post\">
			<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
			<input type=\"hidden\" name=\"section\" value=\"update_page\">
			<br><br><textarea id=\"tiny\" name=\"content\">$row[content]</textarea><br><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"><br></form>";
		}

	}

	public function update_page() {
		$sql = "UPDATE `pages` SET `content` = '$_POST[content]' WHERE `id` = '$_POST[id]'";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The page was updated.</font><br>";
		} else {
			print "<br><font color=red>The page failed to update.</font><br>";
		}
		$_GET['id'] = $_POST['id'];
		$this->page();

	}

	public function refund() {
		print "<h2>Refund</h2>
		A refund can only take place on a ticket that has not already been paid out to the event holder. If the event has already been paid then the customer will need to ask the event holder for a refund.<br><br>
		<form action=\"admin.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"search\">
		<table class=\"table\">
		<tr><td colspan=2>Search Customer:</td></tr>
		<tr><td>Name:</td><td><input type=\"text\" name=\"name\" size=40></td></tr>
		<tr><td>Email:</td><td><input type=\"text\" name=\"email\" size=40></td></tr>
		<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Search\"></td></tr>
		</table>
		</form>";

	}

	public function search() {
		print "<h2>Search Results: $_POST[name] $_POST[email]</h2>";

		$sql = "
		SELECT
			`cart`.`viewID`,
			`events`.`title`,
			`events`.`id`,
			`cart`.`id` AS 'cartID',
			DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
			`cart`.`consumed`,
			`cart`.`price`,
			`cart`.`qty`,
			DATE_FORMAT(`cart`.`date`, '%m/%d/%Y') AS 'date_customer_paid'

		FROM
			`cart`,`events`

		WHERE
			`cart`.`event_paid` = ''
			AND `cart`.`name` LIKE '%$_POST[name]%'
			AND `cart`.`email` LIKE '%$_POST[email]%'
			AND `cart`.`eventID` = `events`.`id`
		";

		print "<table class=\"table\">
		<tr>
			<td><b>Event</b></td>
			<td><b>Event Date</b></td>
			<td><b>Ticket Consumed?</b></td>
			<td><b>Ticket Price</b></td>
			<td><b>QTY</b></td>
			<td><b>Date Paid</b></td>
			<td>&nbsp;</td>
		</tr>";


		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {

                        // discounts
                        $total_discount = "";

                        $sql2 = "SELECT * FROM `cart_discount` WHERE `viewID` = '$row[viewID]' AND `eventID` = '$row[id]'";
                        $result2 = $this->new_mysql($sql2);
                        while ($row2 = $result2->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row2['amount_off'];
                                $type = $row2['type'];
                        }
                        if ($Dfound == "1") {
                                $total = $row['price'];
                                switch ($type) {
                                        case "percent":
                                        $t1 = $amount_off / 100;
                                        $t2 = $total * $t1;
                                        $total = $total - $t2;
                                        $total_discount = $t2;
                                        break;

                                        case "dollar":
                                        $total = $total - $amount_off;
                                        $total_discount = $amount_off;
                                        break;
                                }
                        }

                        if ($total_discount > 0) {
                                $row['price'] = $row['price'] - $total_discount;
                        }


			print "<tr>
			<td><a href=\"index.php?section=page_view&id=$row[id]\" target=_blank>$row[title]</a></td>
			<td>$row[start_date] to $row[end_date]</td>
			<td>$row[consumed]</td>
			<td>$$row[price]</td>
			<td>$row[qty]</td>
			<td>$row[date_customer_paid]</td>
			<td><input type=\"button\" class=\"btn btn-primary\" value=\"Refund\" onclick=\"if(confirm('WARNING: You are about to refund $row[title] for the amount of $$row[price]. Please log into your merchant account and process the refund. Clicking OK will remove this item from the cart system.')){document.location.href='admin.php?section=process_refund&cartID=$row[cartID]'}\"></td>
			</tr>";
			$found = "1";
		}
		if ($found != "1") {
			print "<tr><td colspan=7><font color=blue>Sorry, no records found.</font></td></tr>";
		}
		print "</table>";

	}

	public function process_refund() {

		$sql1 = "SELECT * FROM `cart` WHERE `id` = '$_GET[cartID]'";
		$result1 = $this->new_mysql($sql1);
		while ($row1 = $result1->fetch_assoc()) {
			foreach ($row1 as $key=>$value) {
				$top .= "`$key`,";
				$bot .= "'$value',";
			}
			$top = substr($top,0,-1);
			$bot = substr($bot,0,-1);
			$sql2 = "INSERT INTO `cart_refund` ($top) VALUES ($bot)";
			$result2 = $this->new_mysql($sql2);
			$sql3 = "DELETE FROM `cart` WHERE `id` = '$_GET[cartID]'";
			$result3 = $this->new_mysql($sql3);
			if ($result3 == "TRUE") {
				print "<br><bR><font color=green>The ticket has been removed from the system. If you have not already done so please log into your merchant account and process the refund.<br><br>Please write down this confirmation number: <b>$_GET[cartID]</b><br><br></font>";
			} else {
				print "<br><br><font color=red>There was an error removing the ticket from the system.</font><br><br>";
			}
		}

	}


	public function balance_report($type='tickets',$early='No') {
		print "<h2>Balance Report $type</h2>
		<a href=\"admin.php?section=balance_report&type=$type&early=Yes\">Include early payouts in this report</a><br><br>
		Only events that have not been paid out, has a balance and the event close date has passed will be displayed here.<br><br>";

		if ($_GET['early'] == "Yes") {
			$start_date = date("Ymd");
			$date = DateTime::createFromFormat('Ymd',$start_date);
			$date->modify('+14 day');
			$today = $date->format('Ymd');

		} else {
			$today = date("Ymd");
		}

		if ($type == "tickets") {
		$sql = "
		SELECT
			`cart`.`viewID`,
			`events`.`id`,
			`events`.`title`,
			SUM(`cart`.`price` * `cart`.`qty`) AS 'price',
			`users`.`fname`,
			`users`.`lname`,
			`users`.`email`,
			`users`.`paypal_email`,
			DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

		FROM
			`cart`,`events`,`users`

		WHERE
			`cart`.`eventID` = `events`.`id`
			AND DATE_FORMAT(`events`.`end_date`, '%Y%m%d') < '$today'
			AND `cart`.`status` = 'Paid'
			AND `events`.`userID` = `users`.`id`
			AND `cart`.`event_paid` = ''

		GROUP BY `cart`.`eventID`

		ORDER BY `events`.`title` ASC

		";
		}
                if ($type == "donations") {
		// testing
		//$today = "20161221";
		// end testing

                $sql = "
                SELECT
                        `events`.`id`,
                        `events`.`title`,
                        SUM(`donate`.`price`) AS 'price',
			COUNT(`donate`.`id`) AS 'total',
                        `users`.`fname`,
                        `users`.`lname`,
                        `users`.`email`,
                        `users`.`paypal_email`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `donate`,`events`,`users`

                WHERE
                        `donate`.`eventID` = `events`.`id`
                        AND DATE_FORMAT(`events`.`end_date`, '%Y%m%d') < '$today'
                        AND `donate`.`status` = 'Paid'
                        AND `events`.`userID` = `users`.`id`
                        AND `donate`.`event_paid` = ''

                GROUP BY `donate`.`eventID`

                ORDER BY `events`.`title` ASC

                ";

                }


		print "<table class=\"table\">
		<tr bgcolor=#FFFFFF>
			<td><b>Event</b></td>
			<td><b>Start Date</b></td>
			<td><b>End Date</b></td>
			<td><b>Event Contact</b></td>
			<td><b>PayPal Email</b></td>
			<td><b>Amount To Pay</b></td>
			<td><b>Process Payment</b></td>
		</tr>";

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
                        $i++;
                        if ($i % 2) {
                                $bgcolor = "#E6E6E6";
                        } else {
                                $bgcolor = "#FFFFFF";
                        }

			if ($type == "donations") {
				$fees = $this->get_fees($row['price'],$row['total']);
				$row['price'] = $row['price'] - $fees;
			}

			// discounts
			$total_discount = "";

			$sql2 = "SELECT * FROM `cart_discount` WHERE `viewID` = '$row[viewID]' AND `eventID` = '$row[id]'";
			$result2 = $this->new_mysql($sql2);
			while ($row2 = $result2->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row2['amount_off'];
                                $type = $row2['type'];
			}
                        if ($Dfound == "1") {
				$total = $row['price'];
                                switch ($type) {
                                        case "percent":
                                        $t1 = $amount_off / 100;
                                        $t2 = $total * $t1;
                                        $total = $total - $t2;
                                        $total_discount = $t2;
                                        break;

                                        case "dollar":
                                        $total = $total - $amount_off;
                                        $total_discount = $amount_off;
                                        break;
                                }
                        }

			if ($total_discount > 0) {
				$row['price'] = $row['price'] - $total_discount;
			}

			print "<tr bgcolor=$bgcolor>
				<td><a href=\"index.php?section=page_view&id=$row[id]\" target=_blank>$row[title]</a></td>
				<td>$row[start_date]</td>
				<td>$row[end_date]</td>
				<td>$row[fname] $row[lname]</td>
				<td>$row[paypal_email]</td>
				<td>$".number_format($row['price'],2,'.',',')."</td>
				<td><input type=\"button\" class=\"btn btn-primary\" value=\"Process Payment\" onclick=\"document.location.href='admin.php?section=payout&eventID=$row[id]&type=$type'\"></td>
			</tr>";
			$found = "1";
		}
		if ($found != "1") {
			print "<tr><td colspan=7><font color=blue>No payments are due at this time.</font></td></tr>";
		}
		print "</table>";

	}

        public function get_fees($charge,$tickets) {
                $processor = "0.015";
                $service_fee = "0.50";
                $transaction = "0.02";
                $t1 = $charge * $processor;
                $t2 = $service_fee * $tickets;
                $t3 = ($charge + $t1 + $t2) * $transaction;
                $t4 = $t1 + $t2 + $t3;

                return $t4;

        }


	public function payout($type) {
		print "<h2>Process Payment</h2>";

		if (($type == "percent") or ($type == "dollars")) {
			$type2 = $_GET['type'];
			$type = "tickets";
		}

		if ($type == "tickets") {
                $sql = "
                SELECT
			`cart`.`viewID`,
                        `events`.`id`,
                        `events`.`title`,
                        SUM(`cart`.`price` * `cart`.`qty`) AS 'price',
                        `users`.`fname`,
                        `users`.`lname`,
                        `users`.`email`,
                        `users`.`paypal_email`,
                        `users`.`tax_id`,
                        `users`.`payment_method`,
                        `users`.`ach_routing`,
                        `users`.`ach_number`,
                        `users`.`mail_by_check`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `cart`,`events`,`users`

                WHERE
			`cart`.`eventID` = '$_GET[eventID]'
                        AND `cart`.`eventID` = `events`.`id`
                        AND `cart`.`status` = 'Paid'
                        AND `events`.`userID` = `users`.`id`

                GROUP BY `cart`.`eventID`

                ORDER BY `events`.`title` ASC
                ";
		}

		if ($type == "donations") {
                $sql = "
                SELECT
                        `events`.`id`,
                        `events`.`title`,
                        SUM(`donate`.`price`) AS 'price',
			COUNT(`donate`.`id`) AS 'total',
                        `users`.`fname`,
                        `users`.`lname`,
                        `users`.`email`,
                        `users`.`paypal_email`,
			`users`.`tax_id`,
			`users`.`payment_method`,
			`users`.`ach_routing`,
			`users`.`ach_number`,
			`users`.`mail_by_check`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `donate`,`events`,`users`

                WHERE
                        `donate`.`eventID` = '$_GET[eventID]'
                        AND `donate`.`eventID` = `events`.`id`
                        AND `donate`.`status` = 'Paid'
                        AND `events`.`userID` = `users`.`id`

                GROUP BY `donate`.`eventID`

                ORDER BY `events`.`title` ASC
                ";

		}

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {

                        if ($type == "donations") {
                                $fees = $this->get_fees($row['price'],$row['total']);
                                $row['price'] = $row['price'] - $fees;
                        }

                        // discounts
                        $total_discount = "";

                        $sql2 = "SELECT * FROM `cart_discount` WHERE `viewID` = '$row[viewID]' AND `eventID` = '$row[id]'";
                        $result2 = $this->new_mysql($sql2);
                        while ($row2 = $result2->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row2['amount_off'];
                                $type = $row2['type'];
                        }
                        if ($Dfound == "1") {
                                $total = $row['price'];
                                switch ($type) {
                                        case "percent":
                                        $t1 = $amount_off / 100;
                                        $t2 = $total * $t1;
                                        $total = $total - $t2;
                                        $total_discount = $t2;
                                        break;

                                        case "dollar":
                                        $total = $total - $amount_off;
                                        $total_discount = $amount_off;
                                        break;
                                }
                        }

                        if ($total_discount > 0) {
                                $row['price'] = $row['price'] - $total_discount;
                        }


	                $tax_id = $this->encrypt_decrypt('decrypt',$row['tax_id']);
			$ach_routing = $this->encrypt_decrypt('decrypt',$row['ach_routing']);
			$ach_number = $this->encrypt_decrypt('decrypt',$row['ach_number']);


			switch ($row['payment_method']) {
				case "PayPal":
				$payment_info = "Payment Method: PayPal<br>PayPal Email: $row[paypal_email]<br>";
			
				break;

				case "ACH":
				$payment_info = "Payment Method: ACH<br>Routing Number: $ach_routing<br>Account Number: $ach_number<br>";
				break;

				case "Check":
				$payment_info = "Payment Method: Check<br>$row[mail_by_check]<br>";
				break;
			}


			$amount = number_format($row['price'],2,'.',',');
			print "<b>Event: $row[title]</b><br><br>
			Amount $$amount<br><br>
			Make payments to:<br>
			$row[fname] $row[lname]<br>
			Email: $row[email]<br>
			<br>$payment_info<br>
			<br>Tax ID: $tax_id<br><br>
			Once you have processed the payment click on the button below to mark the payment as paid.<br><br>
			<input type=\"button\" class=\"btn btn-success\" value=\"Record Payment and Mark As Paid\" onclick=\"document.location.href='admin.php?section=mark_as_paid&eventID=$_GET[eventID]&type=$type'\"><br>";
			
		}
	}

        public function encrypt_decrypt($action, $string) {
            $output = false;

            $encrypt_method = "AES-256-CBC";
            $secret_key = 'thefoxranoverthewhiteplansintothemouthofabear';
            $secret_iv = 'randomstuffisgoodtoknowcauseitishardertocrackthenasimplepassword';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            if( $action == 'encrypt' ) {
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            }
            else if( $action == 'decrypt' ){
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

            return $output;
        }


	public function mark_as_paid($type) {

		if ($type == "tickets") {
                $sql = "
                SELECT
                        `events`.`id`,
                        `events`.`title`,
                        SUM(`cart`.`price` * `cart`.`qty`) AS 'price',
                        `users`.`fname`,
                        `users`.`lname`,
                        `users`.`email`,
                        `users`.`paypal_email`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `cart`,`events`,`users`

                WHERE
                        `cart`.`eventID` = '$_GET[eventID]'
                        AND `cart`.`eventID` = `events`.`id`
                        AND `cart`.`status` = 'Paid'
                        AND `events`.`userID` = `users`.`id`

                GROUP BY `cart`.`eventID`

                ORDER BY `events`.`title` ASC

                ";
		}

		if ($type == "donations") {
                $sql = "
                SELECT
                        `events`.`id`,
                        `events`.`title`,
                        SUM(`donate`.`price`) AS 'price',
			COUNT(`donate`.`id`) AS 'total',
                        `users`.`fname`,
                        `users`.`lname`,
                        `users`.`email`,
                        `users`.`paypal_email`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `donate`,`events`,`users`

                WHERE
                        `donate`.`eventID` = '$_GET[eventID]'
                        AND `donate`.`eventID` = `events`.`id`
                        AND `donate`.`status` = 'Paid'
                        AND `events`.`userID` = `users`.`id`

                GROUP BY `donate`.`eventID`

                ORDER BY `events`.`title` ASC
                ";

		}
		$today = date("Ymd");
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        if ($type == "donations") {
                                $fees = $this->get_fees($row['price'],$row['total']);
                                $row['price'] = $row['price'] - $fees;
                        }

			$sql2 = "INSERT INTO `payout` (`eventID`,`amount_paid`,`date_paid`) VALUES ('$_GET[eventID]','$row[price]','$today')";
			$result2 = $this->new_mysql($sql2);
			if ($result2 == "TRUE") {
				if ($type == "tickets") {
					$sql3 = "UPDATE `cart` SET `event_paid` = 'Yes', `event_paid_date` = '$today' WHERE `eventID` = '$_GET[eventID]' AND `status` = 'Paid'";
				}
				if ($type == "donations") {
                                        $sql3 = "UPDATE `donate` SET `event_paid` = 'Yes', `event_paid_date` = '$today' WHERE `eventID` = '$_GET[eventID]' AND `status` = 'Paid'";
				}
				$result3 = $this->new_mysql($sql3);
				if ($result3 == "TRUE") {
					print "<br><br><br><font color=green>The event payout is complete.</font><br>";
					$this->balance_report();
				} else {
					print "<br><font color=red>ERROR: unable to update cart table.</font><br>";
				}
			} else {
				print "<br><font color=red>ERROR updating payout table.</font><br>";
			}
		}

	}

	public function admin_users() {
		print "<h2>Admin Users</h2>
                <table class=\"table\">
                <tr><td colspan=2><input type=\"button\" class=\"btn btn-success\" value=\"New Admin User\" onclick=\"document.location.href='admin.php?section=new_admin_user'\"></td></tr>
                <tr><td><b>Name</b></td><td>&nbsp;</td></tr>";

                $sql = "SELECT * FROM `admin_users` ORDER BY `lname` ASC, `fname` ASC";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i++;
                        if ($i % 2) {
                                $bgcolor = "#E6E6E6";
                        } else {
                                $bgcolor = "#FFFFFF";
                        }
                        print "<tr bgcolor=$bgcolor><td>$row[fname] $row[lname]</td>
                        <td><input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='admin.php?section=edit_admin_user&id=$row[id]'\">&nbsp;";
			if ($_SESSION['admin_uuname'] != $row['uuname']) {
	                        print "<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[fname] $row[lname]')){document.location.href='admin.php?section=delete_admin_user&id=$row[id]'};\">";
			}
                        print "</td></tr>";
                        $found2 = "1";
                }
                print "</table>";

	}

	public function new_admin_user() {
                print "<h2>New Admin User</h2>";

                print "<form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"save_admin_user\">
                <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";

                        print "<table class=\"table\">
                        <tr><td>username:</td><td><input type=\"text\" name=\"uuname\" size=40></td></tr>
                        <tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" value=\"$row[fname]\" size=40></td></tr>
                        <tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" value=\"$row[lname]\" size=40></td></tr>
                        <tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
                        <tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
                        <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></td></tr>
                        </table>
                        </form>";

	}

	public function save_admin_user() {
		$sql = "INSERT INTO `admin_users` (`uuname`,`uupass`,`fname`,`lname`,`email`,`status`) VALUES ('$_POST[uuname]','$_POST[uupass]','$_POST[fname]','$_POST[lname]','$_POST[email]','Yes')";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The admin user was added.</font><br>";
		} else {
			print "<br><font color=red>The admin user failed to add.</font><br>";
		}
		$this->admin_users();
	}

	public function edit_admin_user() {
                print "<h2>Edit Admin User</h2>";

                print "<form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"update_admin_user\">
                <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";


                $sql = "SELECT * FROM `admin_users` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
	                print "<table class=\"table\">
	                <tr><td>username:</td><td>$row[uuname]</td></tr>
			<tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" value=\"$row[fname]\" size=40></td></tr>
			<tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" value=\"$row[lname]\" size=40></td></tr>
			<tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
			<tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
        	        <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"></td></tr>
	                </table>
	                </form>";
		}


	}

	public function update_admin_user() {
		$sql = "UPDATE `admin_users` SET `fname` = '$_POST[fname]', `lname` = '$_POST[lname]', `email` = '$_POST[email]', `uupass` = '$_POST[uupass]' WHERE `id` = '$_POST[id]'";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The admin user was updated.</font><br>";
		} else {
			print "<bR><font color=red>The admin user failed to update.</font><br>";
		}
		$this->admin_users();
	}

	public function delete_admin_user() {
		$sql = "DELETE FROM `admin_users` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The admin user was deleted.</font><br>";
                } else {
                        print "<bR><font color=red>The admin user failed to delete.</font><br>";
                }
                $this->admin_users();

	}

        public function locations() {
                print "<h2>Locations</h2>
                <table class=\"table\">
                <tr><td colspan=2><input type=\"button\" class=\"btn btn-success\" value=\"New Location\" onclick=\"document.location.href='admin.php?section=new_location'\"></td></tr>
                <tr><td><b>Name</b></td><td>&nbsp;</td></tr>";

                $sql = "SELECT * FROM `location` ORDER BY `location` ASC";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i++;
                        if ($i % 2) {
                                $bgcolor = "#E6E6E6";
                        } else {
                                $bgcolor = "#FFFFFF";
                        }
                        print "<tr bgcolor=$bgcolor><td>$row[location]</td>
                        <td><input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='admin.php?section=edit_location&id=$row[id]'\">&nbsp;";
                        $sql2 = "SELECT * FROM `events` WHERE `locationID` = '$row[id]'";
                        $result2 = $this->new_mysql($sql2);
                        $found1 = "0";
                        while ($row2 = $result2->fetch_assoc()) {
                                $found1 = "1";
                        }
                        if ($found1 == "1") {
                                print "<input type=\"button\" class=\"btn btn-warning\" value=\"Delete\" onclick=\"alert('Sorry, the location is in use with another event')\">";
                        } else {
                                print "<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[location]')){document.location.href='admin.php?section=delete_location&id=$row[id]'};\">";
                        }
                        print "</td></tr>";
                        $found2 = "1";
                }
                if ($found2 != "1") {
                        print "<tr><td colspan=2><font color=blue>Sorry, there are no locations. Please add one.</font></td></tr>";
                }
                print "</table>";


        }


	public function categories() {
		print "<h2>Categories</h2>
		<table class=\"table\">
                <tr><td colspan=2><input type=\"button\" class=\"btn btn-success\" value=\"New Category\" onclick=\"document.location.href='admin.php?section=new_category'\"></td></tr>
                <tr><td><b>Name</b></td><td>&nbsp;</td></tr>";

                $sql = "SELECT * FROM `category` ORDER BY `category` ASC";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i++;
                        if ($i % 2) {
                                $bgcolor = "#E6E6E6";
                        } else {
                                $bgcolor = "#FFFFFF";
                        }
                        print "<tr bgcolor=$bgcolor><td>$row[category]</td>
                        <td><input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='admin.php?section=edit_category&id=$row[id]'\">&nbsp;";
                        $sql2 = "SELECT * FROM `events` WHERE `categoryID` = '$row[id]'";
                        $result2 = $this->new_mysql($sql2);
                        $found1 = "0";
                        while ($row2 = $result2->fetch_assoc()) {
                                $found1 = "1";
                        }
                        if ($found1 == "1") {
                                print "<input type=\"button\" class=\"btn btn-warning\" value=\"Delete\" onclick=\"alert('Sorry, the category is in use with another event')\">";
                        } else {
                                print "<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[category]')){document.location.href='admin.php?section=delete_category&id=$row[id]'};\">";
                        }
                        print "</td></tr>";
                        $found2 = "1";
                }
                if ($found2 != "1") {
                        print "<tr><td colspan=2><font color=blue>Sorry, there are no categories. Please add one.</font></td></tr>";
                }
                print "</table>";


	}

	public function account_types() {

		print "<h2>Account Types</h2><br>

		<table class=\"table\">
		<tr><td colspan=2><input type=\"button\" class=\"btn btn-success\" value=\"New Type\" onclick=\"document.location.href='admin.php?section=new_type'\"></td></tr>
		<tr><td><b>Name</b></td><td>&nbsp;</td></tr>";

		$sql = "SELECT * FROM `account_types` ORDER BY `description` ASC";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
                        $i++;
                        if ($i % 2) {
                                $bgcolor = "#E6E6E6";
                        } else {
                                $bgcolor = "#FFFFFF";
                        }
			print "<tr bgcolor=$bgcolor><td>$row[description]</td>
			<td><input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='admin.php?section=edit_type&id=$row[id]'\">&nbsp;";
			$sql2 = "SELECT * FROM `users` WHERE `account_type` = '$row[id]'";
			$result2 = $this->new_mysql($sql2);
			$found1 = "0";
			while ($row2 = $result2->fetch_assoc()) {
				$found1 = "1";
			}
			if ($found1 == "1") {
				print "<input type=\"button\" class=\"btn btn-warning\" value=\"Delete\" onclick=\"alert('Sorry, the account type is in use with another user')\">";
			} else {
				print "<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[description]')){document.location.href='admin.php?section=delete_type&id=$row[id]'};\">";
			}
			print "</td></tr>";
			$found2 = "1";
		}
		if ($found2 != "1") {
			print "<tr><td colspan=2><font color=blue>Sorry, there are no account types. Please add one.</font></td></tr>";
		}
		print "</table>";

	}

	public function new_type() {
		print "<h2>New Account Type</h2>
		<form action=\"admin.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"save_type\">
		<table class=\"table\">
		<tr><td>Description:</td><td><input type=\"text\" name=\"description\" size=40></td></tr>
		<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></td></tr>
		</table>
		</form>";

	}

        public function new_category() {
                print "<h2>New Category</h2>
                <form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"save_category\">
                <table class=\"table\">
                <tr><td>Category:</td><td><input type=\"text\" name=\"category\" size=40></td></tr>
                <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></td></tr>
                </table>
                </form>";

        }

        public function new_location() {
                print "<h2>New Location</h2>
                <form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"save_location\">
                <table class=\"table\">
                <tr><td>Location:</td><td><input type=\"text\" name=\"location\" size=40></td></tr>
                <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></td></tr>
                </table>
                </form>";

        }

        public function save_location() {
                $sql = "INSERT INTO `location` (`location`) VALUES ('$_POST[location]')";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The location was added.</font><br>";
                } else {
                        print "<br><font color=red>The location failed to add.</font><br>";
                }
                $this->locations();


        }



        public function save_category() {
                $sql = "INSERT INTO `category` (`category`) VALUES ('$_POST[category]')";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The category was added.</font><br>";
                } else {
                        print "<br><font color=red>The category failed to add.</font><br>";
                }
                $this->categories();


        }


	public function save_type() {
		$sql = "INSERT INTO `account_types` (`description`) VALUES ('$_POST[description]')";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The account type was added.</font><br>";
                } else {
                        print "<br><font color=red>The account type failed to add.</font><br>";
                }
                $this->account_types();


	}

	public function delete_type() {
		$sql = "DELETE FROM `account_types` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The account type was deleted.</font><br>";
                } else {
                        print "<br><font color=red>The account type failed to delete.</font><br>";
                }
                $this->account_types();


	}

        public function delete_location() {
                $sql = "DELETE FROM `location` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The location was deleted.</font><br>";
                } else {
                        print "<br><font color=red>The location failed to delete.</font><br>";
                }
                $this->locations();


        }


        public function delete_category() {
                $sql = "DELETE FROM `category` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The category was deleted.</font><br>";
                } else {
                        print "<br><font color=red>The category failed to delete.</font><br>";
                }
                $this->categories();


        }

	public function edit_type() {
		print "<h2>Edit Account Type</h2>";

		print "<form action=\"admin.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"update_type\">
		<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";


		$sql = "SELECT * FROM `account_types` WHERE `id` = '$_GET[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$description = $row['description'];
		}

		print "<table class=\"table\">
		<tr><td>Description:</td><td><input type=\"text\" name=\"description\" value=\"$description\" size=40></td></tr>
		<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"></td></tr>
		</table>
		</form>";
		
	}

        public function edit_category() {
                print "<h2>Edit Category</h2>";

                print "<form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"update_category\">
                <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";


                $sql = "SELECT * FROM `category` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $category = $row['category'];
                }

                print "<table class=\"table\">
                <tr><td>Category:</td><td><input type=\"text\" name=\"category\" value=\"$category\" size=40></td></tr>
                <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"></td></tr>
                </table>
                </form>";

        }


        public function edit_location() {
                print "<h2>Edit Location</h2>";

                print "<form action=\"admin.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"update_location\">
                <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";


                $sql = "SELECT * FROM `location` WHERE `id` = '$_GET[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $location = $row['location'];
                }

                print "<table class=\"table\">
                <tr><td>Location:</td><td><input type=\"text\" name=\"location\" value=\"$location\" size=40></td></tr>
                <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"></td></tr>
                </table>
                </form>";

        }


	public function update_type() {
		$sql = "UPDATE `account_types` SET `description` = '$_POST[description]' WHERE `id` = '$_POST[id]'";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The account type was updated.</font><br>";
		} else {
			print "<br><font color=red>The account type failed to update.</font><br>";
		}
		$this->account_types();
	}

        public function update_category() {
                $sql = "UPDATE `category` SET `category` = '$_POST[category]' WHERE `id` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The category was updated.</font><br>";
                } else {
                        print "<br><font color=red>The category failed to update.</font><br>";
                }
                $this->categories();
        }

        public function update_location() {
                $sql = "UPDATE `location` SET `location` = '$_POST[location]' WHERE `id` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<br><font color=green>The location was updated.</font><br>";
                } else {
                        print "<br><font color=red>The location failed to update.</font><br>";
                }
                $this->locations();
        }


	public function users() {
		print "<h2>Users</h2>";

		print "<input type=\"button\" class=\"btn btn-success\" value=\"Add User\" onclick=\"document.location.href='admin.php?section=new_user'\"><br>";

		print "<table class=\"table\">
		<tr>
			<td><b>Username</b></td>
			<td><b>Email</b></td>
			<td><b>Active</b></td>
			<td><b>Account</b></td>
			<td>&nbsp;</td>
		</tr>";
		$sql = "
		SELECT
			`users`.`id`,
			`account_types`.`description`,
			`users`.`uuname`,
			`users`.`email`,
			`users`.`active`
		FROM
			`users`,`account_types`
		WHERE
			`users`.`account_type` = `account_types`.`id`
		";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$i++;
			if ($i % 2) {
				$bgcolor = "#E6E6E6";
			} else {
				$bgcolor = "#FFFFFF";
			}
			print "<tr bgcolor=$bgcolor>
				<td>$row[uuname]</td>
				<td>$row[email]</td>
				<td>$row[active]</td>
				<td>$row[description]</td>
				<td>
					<form name=\"myform$row[id]\">
					<input type=\"hidden\" name=\"id\" value=\"$row[id]\">
					<input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='admin.php?section=edit_user&id=$row[id]'\"> 
					<input type=\"button\" class=\"btn btn-danger\" onclick=\"if(confirm('You are about to delete $row[uuname]. This will DELETE all events and tickets for this user. Once you delete the user it is not possible to un-delete the user.') ) {delete_user(this.form);}\" value=\"Delete\">
					</form>
				</td>
			</tr>";
			$found = "1";
		}
		if ($found != "1") {
			print "<tr><td colspan=5><center><font color=blue>There are no users.</font></center></td></tr>";
		}
		print "</table>";
		?>
                                <script>
                                 function delete_user(myform) {
                                        $.get('delete_user.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
						location.reload();
                                        });
                                 }
				</script>

		<?php
	}

	public function new_user() {
		print "<h2>New User</h2>";

                $sql = "SELECT * FROM `account_types`";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $account_types .= "<option value=\"$row[id]\">$row[description]</option>";
                }


		print "<form action=\"admin.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"save_user\">
		<table class=\"table\">
                <tr><td>Username:</td><td><input type=\"text\" name=\"uuname\" size=40></td></tr>
                <tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
                <tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
                <tr><td>Account Type:</td><td><select name=\"account_type\">$account_types</select></td></tr>
                <tr><td>Active:</td><td><select name=\"active\"><option>Yes</option><option>No</option></select></td></tr>
                <tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save User\"></td></tr>
		</table>
		</form>
                        ";

	}

	public function save_user() {
		$sql = "SELECT * FROM `users` WHERE `uuname` = '$_POST[uuname]'";
		$result = $this->new_mysql($sql);
		$total = $result->number_rows;
		if ($total > 0) {
			$err = "1";
		}

		$sql = "SELECT * FROM `users` WHERE `email` = '$_POST[email]'";
                $result = $this->new_mysql($sql);
                $total = $result->number_rows;
                if ($total > 0) {
                        $err = "2";
                }

		switch ($err) {
			case "1":
			print "<br><font color=red>The username you entered is not available.</font><br>";
			$this->new_user();
			die;
			break;

			case "2":
			print "<br><font color=red>The email you entered is not available.</font><br>";
                        $this->new_user();
                        die;
                        break;
		}

		if ($err == "") {
			$sql = "INSERT INTO `users` (`uuname`,`uupass`,`email`,`active`,`verified`,`account_type`) VALUES ('$_POST[uuname]','$_POST[uupass]','$_POST[email]','Yes','Yes','$_POST[account_type]')";
	                $result = $this->new_mysql($sql);
			if ($result == "TRUE") {
				print "<br><font color=green>The user was added.</font><br>";
			} else {
				print "<br><font color=red>There was an error saving the user.</font><br>";
			}
		} else {
			print "<br><font color=red>There was an unknown error.</font><br>";
		}
		$this->users();

		
	}

	public function edit_user() {

		$sql = "SELECT * FROM `account_types`";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
			$account_types .= "<option value=\"$row[id]\">$row[description]</option>";
		}

		$sql = "
		SELECT
			`account_types`.`description`,
			`users`.`uuname`,
			`users`.`uupass`,
			`users`.`email`,
			`users`.`active`,
			`users`.`account_type`

		FROM
			`users`,`account_types`

		WHERE
			`users`.`account_type` = `account_types`.`id`
			AND `users`.`id` = '$_GET[id]'
		";
		$result = $this->new_mysql($sql);

		print "<h2>Update User</h2>";

		print "<form action=\"admin.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"update_user\">
		<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
		<table class=\"table\">";

		while ($row = $result->fetch_assoc()) {
			print "<tr><td>Username:</td><td>$row[uuname]</td></tr>
			<tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
			<tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
			<tr><td>Account Type:</td><td><select name=\"account_type\"><option selected value=\"$row[account_type]\">$row[description]</option>$account_types</select></td></tr>
			<tr><td>Active:</td><td><select name=\"active\"><option selected>$row[active]</option><option>Yes</option><option>No</option></select></td></tr>
			<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update User\"></td></tr>
			";
		}
		print "</table></form>";
	}

	public function update_user() {
		$sql = "UPDATE `users` SET `email` = '$_POST[email]', `uupass` = '$_POST[uupass]', `active` = '$_POST[active]', `account_type` = '$_POST[account_type]' WHERE `id` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The user was updated.</font><br>";
		} else {
			print "<br><font color=red>The user failed to update.</font><br>";
		}
		$this->users();
	}

}
}
?>
