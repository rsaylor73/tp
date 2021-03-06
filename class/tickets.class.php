<?php

if( !class_exists( 'Tickets')) {
class Tickets {
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


        public function pie_chart_v2($id,$name,$data,$title,$drilldown) {

                $pie_chart = "
                <script type=\"text/javascript\">
                $(function () {
                    // Create the chart
                    $('#$id').highcharts({
                        chart: {
                            type: 'pie'
                        },
                        title: {
                            text: '$title'
                        },
                        subtitle: {
                            text: 'Click to drilldown.'
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },

                        tooltip: {
                            headerFormat: '<span style=\"font-size:11px\">{series.name}</span><br>',
                            pointFormat: '<span style=\"color:{point.color}\">{point.name}</span>: <b>{point.y}</b> tickets<br/>'
                        },
                        series: [{
                            name: '$name',
                            colorByPoint: true,
                            data: [
                ";

                $pie_chart .= $data;

                $pie_chart .= "
                                ]
                        }],
                        drilldown: {
                            series: [
                                ".$drilldown."
                        ]
                        }
                    });
                });
                </script>
                ";

                return($pie_chart);
        }


        public function bar_graph_v2($id,$title,$subtitle,$labels,$series1,$s1_title) {

                if (is_array($labels)) {
                        foreach ($labels as $key=>$value) {
                                $categories .= "'$value',";
                        }
                        $categories = trim($categories,',');
                }

                if (is_array($series1)) {
                        foreach ($series1 as $key=>$value) {
                                $s1 .= "$value,";
                        }
                        $s1 = trim($s1,",");
                }

                if (is_array($series2)) {
                        foreach ($series2 as $key=>$value) {
                                $s2 .= "$value,";
                        }
                        $s2 = trim($s2,",");
                }

                $bar_graph = "
                <style type=\"text/css\">
                        ${demo.css}
                </style>
                <script type=\"text/javascript\">
                $(function () {
                    $('#$id').highcharts({
                        chart: {
                            type: 'bar'
                        },
                        title: {
                            text: '$title'
                        },
                        xAxis: {
                            categories: [$categories]
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: '$subtitle'
                            }
                        },
                        legend: {
                            reversed: true
                        },
                        plotOptions: {
                            series: {
                                stacking: 'normal'
                            }
                        },
                        series: [

                        {
                            name: '$s1_title',
                            data: [$s1],
                            color: '#088A08'
                        }
                        ]
                    });
                });
                </script>
                ";

                return($bar_graph);

        }

        // check login system
        public function check_login() {
                $sql = "SELECT `users`.`id` FROM `users` WHERE `users`.`uuname` = '$_SESSION[uuname]' AND BINARY `users`.`uupass` = '$_SESSION[uupass]' AND `users`.`active` = 'Yes'";
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
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <div class="col-md-12 col-lg-6" id="ajax">
                ';
                print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';
		?>
		You have been signed off. Loading...
		<meta http-equiv="refresh" content="3; url=hq">
		<?php
                print '</div></div></div></div>';
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
                                <tr><td>&nbsp;</td><td><input type=\"button\" value=\"Login\" onclick=\"login(this.form)\"></td></tr>
                                <tr><td><a href=\"javascript:void(0)\" onclick=\"forgot_password(this.form)\">Forgot Password?</a></td><td>";

                                print "</td></tr>
                        </table>
                </td></tr>
                </table>
                </form>
                </div>
                <br>";

                ?>
                                <script>
                                 function forgot_password(myform) {
                                        $.get('forgot_password.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#login-scr").html(php_msg);
                                        });
                                 }

                                function login(myform) {
                                        $.get('login.php',
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

	public function upcoming_events() {
                $settings = $this->get_settings();
		$date = date("Ymd");
		$today = date("Y-m-d", strtotime($date . "-14 days"));
		$end_date = date("Y-m-d");
		$sql = "
                SELECT
                        `events`.`title`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`events`.`start_date`, '%W') AS 'weekday',
			DATE_FORMAT(`events`.`start_date`, '%l:%i%p') AS 'timedate',
			DATE_FORMAT(`events`.`start_date`, '%Y') AS 'year',
			DATE_FORMAT(`events`.`start_date`, '%e %b') AS 'date1',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
                        `events`.`cover_image`,
                        `location`.`location`,
                        `events`.`id`,
			`events`.`userID`,
                        `events`.`homepage`

                FROM
                        `events`,`location`

                WHERE
                        `events`.`event_page` = 'public'
                        AND `events`.`locationID` = `location`.`id`
			#AND `events`.`start_date` > '$today'
			AND `events`.`end_date` >= '$end_date'
		LIMIT 8
		";

		$html .= "<div class=\"sh-width\">";

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
                        if ($row['homepage'] != "") {
                                $link = "<a href=\"http://$row[homepage].$settings[8]\"><font color=#096FBB>$row[title]</font></a>";
                                $link1 = "<a href=\"http://$row[homepage].$settings[8]\">";
				$link2 = "</a>";

                        } else {
                                $link = "<a href=\"https://www.$settings[8]/index.php?section=page_view&id=$row[id]\"><font color=#096FBB>$row[title]</font></a>";
                                $link1 = "<a href=\"https://www.$settings[8]/index.php?section=page_view&id=$row[id]\">";
				$link2 = "</a>";
                        }
			$views = $this->get_views($row['id']);
			$cover = "";

			$html .= "
			    <div class=\"event\">
			      <div class=\"event-cover\">
			        <div class=\"event-bg\"></div>
			        <img src=\"uploads/$row[userID]/cover/$row[id]/$row[cover_image]\">

		        <div class=\"event-title\">
		          <div class=\"event-dmy\">
		            $row[date1]<span>$row[year]</span>
		            <div class=\"event-week\">$row[weekday]</div>
		            <div class=\"event-time\">$row[timedate]</div>
		          </div><!-- end .event-dmy -->

		          <h2>
		          <center>
		          <a href=\"https://www.$settings[8]/index.php?section=page_view&id=$row[id]\">$row[title]</a>
		          </center>
		          </h2>
		        </div><!-- end .event-title -->
		        <div class=\"event-info\">
		          <div class=\"event-venue\"></div>
		
		        </div><!-- end .event-info -->

		        <div class=\"event-lv\">
		          <i class=\"fa fa-eye fa-lg\"></i>
		          <div class=\"info-view\">$views</div>
		        </div><!-- end .event-lv -->
		      </div><!-- end .event-cover -->
		    </div><!-- end .event -->
			";



			if ($row['cover_image'] != "") {
				//$cover = "<tr><td colspan=2><center>$link1<img src=\"uploads/$row[userID]/cover/$row[id]/$row[cover_image]\" width=\"300\" height=\"200\"></center>$link2</td></tr>";
			}

			if ($counter == "4") {
				$html .= "</div><div class=\"sh-width\">";
				//$html .= "</tr><tr>";
				$counter = 0;
			}

			

			/*
			$html .= "<td><table border=0 width=300  style=\"display: inline\">
			$cover
			<tr bgcolor=#FFFFFF><td colspan=2><center>$link</center></td></tr>
			<tr bgcolor=#FFFFFF><td colspan=2><center><i class=\"fa fa-map-marker\" font color=\"#99A9C2\"></i> <font color=#99A9C2>$row[location]</font></td></tr>
			<tr bgcolor=#EFEFEF><td align=left><i class=\"fa fa-calendar fa-lg\" style=\"color:#99A9C2\"></i> $row[start_date]</td><td align=right><i class=\"fa fa-eye fa-lg\"></i> $views</td></tr>
			</table></td>";
			*/
			$counter++;
			
			
		}
		$html .= "</div>";
		//$html .= "</tr></table>";

		if ($html == "") {
			$html = "No upcoming events...";
		} else {
			$html .= "<br><br><br><br><br>";
		}



		return $html;

	}

	public function search() {
		$settings = $this->get_settings();

		if ($_GET['start_date'] != "") {
			$sql0 = "AND '$_GET[start_date]'  BETWEEN `events`.`start_date` AND `events`.`end_date`";
		}


		if ($_GET['events'] != "") {
			$sql1 = "AND `events`.`id` = '$_GET[events]'";
		}
		if ($_GET['location'] != "") {
			$sql2 = "AND `events`.`locationID` = '$_GET[location]'";
		}

		if ($_GET['search_string'] != "") {
			$sql2 = "AND `events`.`title` LIKE '%$_GET[search_string]%'";
		}
		$today = date("Ymd");
		$sql = "
		SELECT
			`events`.`title`,
			DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
			`events`.`end_date` AS 'end_date2',
			`events`.`cover_image`,
			`location`.`location`,
                        `events`.`userID`,
			`events`.`id`,
			`events`.`homepage`

		FROM
			`events`,`location`

		WHERE
			`events`.`event_page` = 'public'
			AND `events`.`locationID` = `location`.`id`
			AND DATE_FORMAT(`events`.`end_date`, '%Y%m%d') >= '$today'
			$sql0
			$sql1
			$sql2	

		";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
                        $counter++;
                        if ($row['homepage'] != "") {
                                $link = "<a href=\"http://$row[homepage].$settings[8]\"><font color=#096FBB>$row[title]</font></a>";
                                $link1 = "<a href=\"http://$row[homepage].$settings[8]\">";
                                $link2 = "</a>";

                        } else {
                                $link = "<a href=\"https://www.$settings[8]/index.php?section=page_view&id=$row[id]\"><font color=#096FBB>$row[title]</font></a>";
                                $link1 = "<a href=\"https://www.$settings[8]/index.php?section=page_view&id=$row[id]\">";
                                $link2 = "</a>";
                        }
                        $views = $this->get_views($row['id']);
                        $cover = "";
                        if ($row['cover_image'] != "") {
                                $cover = "<tr><td colspan=2><center>$link1<img src=\"uploads/$row[userID]/cover/$row[id]/$row[cover_image]\" width=\"300\" height=\"200\"></center>$link2</td></tr>";
                        }

                        if ($counter == "5") {
                                $html .= "<br>";
                        }

                        $html .= "<table border=0 width=300  style=\"display: inline\">
                        $cover
                        <tr bgcolor=#FFFFFF><td colspan=2><center>$link</center></td></tr>
                        <tr bgcolor=#FFFFFF><td colspan=2><center><i class=\"fa fa-map-marker\" font color=\"#99A9C2\"></i> <font color=#99A9C2>$row[location]</font></td></tr>
                        <tr bgcolor=#EFEFEF><td align=left><i class=\"fa fa-calendar fa-lg\" style=\"color:#99A9C2\"></i> $row[start_date]</td><td align=right><i class=\"fa fa-eye fa-lg\"></i> $views</td></tr>
                        </table>&nbsp;&nbsp;";

			$found = "1";
		}
		if ($found != "1") {
			$html = "<tr><td colspan=4><font color=blue>Sorry, no results found.</font>";
		}
		return $html;

	}


        // User Dashboard
        public function dashboard() {
		// The same dashboard is used and the dashboard loads based on the member type.
                switch ($_SESSION['userType']) {
                        case "admin":
			case "member":
                        $this->dashboard_admin();
                        break;
                }
        }

        // Admin Dashboard
        private function dashboard_admin() {
                $temp = rand(50,50000);
                include "dashboard_admin.php";
        }


        public function get_settings() {
                // settings
                $sql = "SELECT * FROM `settings` WHERE `id` = '1'";
                $result = $this->new_mysql($sql);
                $row = $result->fetch_assoc();

                $sitename = $row['sitename'];
                $siteurl = $row['siteurl'];
                $site_email = $row['site_email'];

                $base_path = $row['base_path'];
		$server_ip = $row['server_ip'];
		$domain_user = $row['domain_user'];
		$domain_pw = $row['domain_pw'];
		$domain = $row['domain'];
		$ssl = $row['SSL'];
		$cc_username = $row['cc_username'];
		$cc_password = $row['cc_password'];

                // email headers - This is fine tuned, please do not modify
                $header = "MIME-Version: 1.0\r\n";
                $header .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $header .= "From: $sitename <$site_email>\r\n";
                $header .= "Reply-To: $sitename <$site_email>\r\n";
                $header .= "X-Priority: 3\r\n";
                $header .= "X-Mailer: PHP/" . phpversion()."\r\n";

                $data = array();
                $data[] = $sitename;
                $data[] = $siteurl;
                $data[] = $site_email;
                $data[] = $header;
                $data[] = $base_path;
		$data[] = $server_ip;
		$data[] = $domain_user;
		$data[] = $domain_pw;
		$data[] = $domain;
		$data[] = $ssl;
		$data[] = $cc_username;
		$data[] = $cc_password;
                return $data;
        }


	public function profileOLD() {
			$sql = "SELECT * FROM `users` WHERE `id` = '$_SESSION[id]'";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				print "
				<br><h2>Profile</h2>
				<form action=\"index.php\" method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"save_profile\">
				<table class=\"table\">
				<tr><td>First Name:</td><td><input type=\"text\" name=\"first\" value=\"$row[first]\" size=40></td></tr>
				<tr><td>Last Name:</td><td><input type=\"text\" name=\"last\" value=\"$row[last]\" size=40></td></tr>
				<tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
				<tr><td>Username:</td><td>$_SESSION[uuname]</td></tr>
				<tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
	i			<tr><td>Member Type:</td><td><select name=\"userType\"><option selected>$row[userType]</option><option>member</option><option>admin</option></select></td></tr>
				<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update Profile\"></td></tr>
				</table>
				</form>
				";
			}

	}

	public function get_locations($id) {
		$sql = "SELECT * FROM `location` ORDER BY `location` ASC";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			if ($id == $row['id']) {
				$options .= "<option selected value=\"$row[id]\">$row[location]</option>";
			} else {
                                $options .= "<option value=\"$row[id]\">$row[location]</option>";
			}
		}
		return $options;
	}

        public function get_category($id) {
                $sql = "SELECT * FROM `category` ORDER BY `category` ASC";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        if ($id == $row['id']) {
                                $options .= "<option selected value=\"$row[id]\">$row[category]</option>";
                        } else {
                                $options .= "<option value=\"$row[id]\">$row[category]</option>";
                        }
                }
                return $options;
        }


	public function profile() {

		print '
          <div class="user-profile">
            <div class="row">
              <div class="col-md-5">
                <div class="user-display">
                  <div class="user-display-bg"><img src="assets/img/user-profile-display.png" alt="Profile Background"></div>
                  <div class="user-display-bottom">
                    <div class="user-display-avatar"><img src="assets/img/avatar-150.png" alt="Avatar"></div>
                    <div class="user-display-info">
		';



		print "<div class=\"name\">My Profile</div>";

		$sql = "SELECT * FROM `users` WHERE `uuname` = '$_SESSION[uuname]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			print "<form name=\"myform\" action=\"index.php\" method=\"post\">
			<input type=\"hidden\" name=\"section\" value=\"profile\">
			<table class=\"table\">
			<tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" value=\"$row[fname]\" size=40></td></tr>
			<tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" value=\"$row[lname]\" size=40></td></tr>
			<tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\" size=40></td></tr>
			";
                        if ($_SESSION['resellerID'] == "0") {

			// get details
			$row['ach_routing'] = $this->encrypt_decrypt('decrypt',$row['ach_routing']);
			$row['ach_number'] = $this->encrypt_decrypt('decrypt',$row['ach_number']);
			$row['tax_id'] = $this->encrypt_decrypt('decrypt',$row['tax_id']);

            // Mask data
            $tax_id_len = strlen($row['tax_id']);
            $tax_id_4 = substr($row['tax_id'], -4);
            $i = $tax_id_len - 4;
            for ($y=0; $y < $i; $y++) {
                $mask .= "*";
            }
            $tax_id = $mask . $tax_id_4;

            $mask = "";
            $ach_number_len = strlen($row['ach_number']);
            $ach_number_4 = substr($row['ach_number'], -4);
            $i = $ach_number_len - 4;
            for ($y=0; $y < $i; $y++) {
                $mask .= "*";
            }
            $ach_number = $mask . $ach_number_4;

            $mask = "";
            $ach_routing_len = strlen($row['ach_routing']);
            $ach_routing_4 = substr($row['ach_routing'], -4);
            $i = $ach_routing_len - 4;
            for ($y=0; $y < $i; $y++) {
                $mask .= "*";
            }
            $ach_routing = $mask . $ach_routing_4;

			$default = "<option selected value=\"$row[payment_method]\">$row[payment_method] (Default)</option>";
			print "
			<tr><td>Payment Method:</td><td><select name=\"payment_method\">$default<option>PayPal</option><option>ACH</option><option>Check</option></select>
			<tr><td colspan=2><br><b>If you selected PayPal as your payment method please enter in your PayPal email below:</b></td></tr>
			<tr><td>PayPal Email:</td><td><input type=\"text\" name=\"paypal_email\" value=\"$row[paypal_email]\" size=40></td></tr>

			<tr><td colspan=2><br><b>If you selected ACH as your payment method please enter in your banks routing number and account number below. By entering in your banking details you authorize an Electronic Payment into your bank account.</b></td></tr>
			<tr><td>Routing Number:</td><td><input type=\"text\" name=\"ach_routing\" value=\"$ach_routing\" size=40></td></tr>
			<tr><td>Account Number:</td><td><input type=\"text\" name=\"ach_number\" value=\"$ach_number\" size=40></td></tr>

			<tr><td colspan=2><br><b>If you selected Check as your payment method please enter in your address below:</b></td></tr>
			<tr><td colspan=2><textarea name=\"mail_by_check\" cols=80 rows=4 placeholder=\"Please type in your address\">$row[mail_by_check]</textarea></td></tr>

			<tr><td colspan=2><br><b>In order to receive payments your Tax ID is required. Please enter in your Tax ID below. If you do not have a Tax ID then your SSN number is required.</b></td></tr>
			<tr><td>Tax ID:</td><td><input type=\"text\" name=\"tax_id\" value=\"$tax_id\" size=40></td></tr>			

			";
			}
			print "
			<tr><td colspan=2><br><b>Login Details</b></td></tr>
			<tr><td>Username:</td><td>$row[uuname]</td></tr>
			<tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" value=\"$row[uupass]\" size=40></td></tr>
			<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update\"></td></tr>
			</table>
			</form>";
			print "<br><br><br>";

			print "</div></div></div></div></div>";
		}

	}

	public function update_profile() {
		
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
                                <div id="ajax">
                ';

                //print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';
        if (substr($_POST['ach_routing'], 0,1) != "*") {
            $ach_routing = $this->encrypt_decrypt('encrypt',$_POST['ach_routing']);
            $ach_routing_sql = ", `ach_routing` = '$ach_routing'";
        }

        if (substr($_POST['ach_number'], 0,1) != "*") {
            $ach_number = $this->encrypt_decrypt('encrypt',$_POST['ach_number']);
            $ach_number_sql = ", `ach_number` = '$ach_number'";
        }
        if (substr($_POST['tax_id'], 0,1) != "*") {
            $tax_id = $this->encrypt_decrypt('encrypt',$_POST['tax_id']);
            $tax_id_sql = ", `tax_id` = '$tax_id'";
        }


		$sql = "UPDATE `users` SET `fname` = '$_POST[fname]', `lname` = '$_POST[lname]', `email` = '$_POST[email]', `uupass` = '$_POST[uupass]', `paypal_email` = '$_POST[paypal_email]',
		`payment_method` = '$_POST[payment_method]' $ach_routing_sql $ach_number_sql, `mail_by_check` = '$_POST[mail_by_check]' $tax_id_sql
		WHERE `uuname` = '$_SESSION[uuname]'
		";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>Your profile was updated.</font><br><br>";
		} else {
			print "<br><font color=red>Your profile failed to update.</font><br>";
		}
		$this->profile();

		print '</div></div></div></div>';
	}

    public function device_type() {
        //print "TEST: $_SERVER[HTTP_USER_AGENT]<br>";
        //die;
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|iphone|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

	public function details() {
                $device = $this->device_type();
                if ($device == "1") {
                   $width = "100";
                   $height = "150";
                   $height2 = "150";
                   $col = "1"; 
                   $fa = "fa-3x";
                } else {
                    $width = "200";
                    $height = "250";
                    $height2 = "330";
                    $col = "3";
                    $fa = "fa-5x";
                }

                print "<h2>Events</h2>";

                ?>

                <div class="col-md-12 col-lg-6">

                <table width=100%>
                <tr><td>

		<?php
		if ($_SESSION['event_details'] == "Yes") {
		?>

		<table width="<?=$width;?>" height="<?=$height2;?>">
		<tr><td width="<?=$width;?>" height="<?=$height2;?>">
                <button style="width:<?=$width;?>px;height:<?=$height2;?>px;" type="button" class="btn btn-default" onclick="document.location.href='index.php?section=dashboard&center=new_details'">
		<br><br>
                &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus <?=$fa;?>" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;
		<br><br><Br>
                </button>
		</td></tr>
		</table>
		<?php
		}
		?>
		</td>

                <?php
		
		$sql = "
		SELECT
			`l`.`location`,
			`c`.`category`,
			`e`.`title`,
			`e`.`address`,
			`e`.`cover_image`,
			`e`.`id`,
			`e`.`userID`,
			DATE_FORMAT(`e`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`e`.`end_date`, '%m/%d/%Y') AS 'end_date'

		FROM
			`events` e,`location` l, `category` c

		WHERE
			`e`.`locationID` = `l`.`id`
			AND `e`.`categoryID` = `c`.`id`
			AND `e`.`userID` = '$_SESSION[id]'
		";

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			if ($counter2 > $col) {
				print "</tr><tr>";
				$counter2 = "-1";
			}
			print "<td>";
			print "<table border=1 width=$width height=$height>
			<tr><td><table border=0 width=100%>";
            if ($device == "0") {
			    if ($row['cover_image'] != "") {
				    print "<tr><td><img src=\"uploads/$row[userID]/cover/$row[id]/$row[cover_image]\" width=$width height=$height></td></tr>";
			     } else {
				    print "<tr><td width=$width height=$height><center>No Image</center></td></tr>";
			     }
            }
			print "<tr><td align=center>$row[title]</td></tr>
			<tr><td align=center>$row[start_date] to $row[end_date]</td></tr>
			<tr><td align=center valign=bottom>
			";
			if ($_SESSION['event_details'] == "Yes") {
				print "
				<input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='index.php?section=dashboard&center=edit_details&id=$row[id]'\"> 
				<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" onclick=\"if(confirm('WARNING: You are about to delete $row[title]')){document.location.href='index.php?section=dashboard&center=delete_details&id=$row[id]'};\"> 
				";
			}
			print "
			</td></tr>";
			print "</table></td></tr>";
			print "</table>";
			print "</td>";
			$found = "1";
			$counter2++;
		}
		print "</table></div>";

	}

	public function get_templates($id) {
		$sql = "SELECT * FROM `templates`";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			if ($row['id'] == $id) {
				$options .= "<option value=\"$row[id]\" selected>$row[name]</option>";
			} else {
				$options .= "<option value=\"$row[id]\">$row[name]</option>";
			}
		}
		return $options;
	}

	public function new_details() {
		$form = "<input type=\"hidden\" name=\"section\" value=\"save_details\">";
		$btn = "Next Step";
		$this->details_template($form,$btn,$null,'1');
	}

	public function edit_details() {
                $form = "<input type=\"hidden\" name=\"section\" value=\"update_details\"><input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">";
		$btn = "Update";
		$sql = "SELECT * FROM `events` WHERE `id` = '$_GET[id]'";
		$result = $this->new_mysql($sql);
		$post = $result->fetch_assoc();
                $this->details_template($form,$btn,$post,$null);


	}

	public function break_down($ticketID) {
		$total_sold = "0";
		$total_avail = "0";

		$sql = "
		SELECT
			`tickets`.`qty` AS 'total',
			`tickets`.`name`

		FROM
			`tickets`

		WHERE
			`tickets`.`id` = '$ticketID'
		";
                $result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$total_avail = $row['total'];
			$name = $row['name'];
		}

		$sql = "
		SELECT
			SUM(`cart`.`qty`) AS 'total'
		FROM
			`cart`

		WHERE
			`cart`.`ticketID` = '$ticketID'
			AND `cart`.`status` = 'Paid'

		GROUP BY `cart`.`ticketID`
		";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $total_sold = $row['total'];
                }

                // get percentage
                $per_sold = ($total_sold / $total_avail) * 100;

		$html = "
                        <tr>
                                <td width=\"50\">&nbsp;</td>
                                <td width=\"300\"><b>$name</b></td>
                                <td width=\"150\">&nbsp;</td>
                        </tr>
                        <tr valign=top>
                                <td width=\"50\">&nbsp;</td>
                                <td width=\"300\">

                                <div class=\"progress\">
                                        <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"$per_sold\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $per_sold%;\">$per_sold%</div>
                                </div>

                                </td>
                                <td width=\"150\">$total_sold/$total_avail</td>
                        </tr>
		";
		return $html;

	}

	public function detail_report() {
        $device = $this->device_type();

		$sql = "SELECT `title` FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			print "<h2>$row[title] : Detailed Report</h2>";
			print "<input type=\"button\" class=\"btn btn-success\" value=\"Download Ticket List\" onclick=\"window.open('download_all.php?id=$_GET[id]')\"><br>";
			// get total qty
			$sql2 = "
			SELECT 
				SUM(`tickets`.`qty`) AS 'total' 

			FROM `tickets`  

			WHERE 
				`tickets`.`eventID` = '$_GET[id]'

			GROUP BY `tickets`.`eventID`
			";
			$result2 = $this->new_mysql($sql2);
			while ($row2 = $result2->fetch_assoc()) {
				$total_tickets_avail = $row2['total'];
			}

			// get total sold
			$sql2 = "
			SELECT
				SUM(`cart`.`qty`) AS 'total'

			FROM
				`cart`

			WHERE
				`cart`.`eventID` = '$_GET[id]'
				AND `cart`.`status` = 'Paid'

			GROUP BY `cart`.`eventID`
			";
                        $result2 = $this->new_mysql($sql2);
                        while ($row2 = $result2->fetch_assoc()) {
                                $total_tickets_sold = $row2['total'];
                        }



			// event tickets
			$sql = "
			SELECT
			        `e`.`title`,
			        `c`.`description`,
				`c`.`name`,
			        `c`.`price`,
			        `c`.`status`,
			        DATE_FORMAT(`c`.`date`, '%m/%d/%Y') AS 'date'
			FROM
			        `cart` c,
			        `events` e

			WHERE
			        `c`.`eventID` = `e`.`id`
				AND `c`.`eventID` = '$_GET[id]'
			        AND `e`.`userID` = '$_SESSION[id]'
			        AND `c`.`status` = 'Paid'

			ORDER BY `e`.`title` ASC
			";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
			        if ($this_title != $title) {
			                //$html2 .= "<tr><td colspan=4><b><h2>$row[title]</h2></b></td></tr>";
			                $title = $row['title'];
			        }
			        switch ($row['status']) {
			        case "Pending":
			                $status = '<td class="text-warning">Pending</td>';
			        break;
			        case "Paid":
			                $status = '<td class="text-success">Completed</td>';
			        break;
			        }
			        $html2 .= '
			                      <tr>
						<td>'.$row['name'].'</td>
			                        <td>'.$row['description'].'</td>
			                        <td>$'.$row['price'].'</td>
			                        <td>'.$row['date'].'</td>
			                        '.$status.'
			                      </tr>
			        ';
			}









			// get percentage
			$per_sold = ($total_tickets_sold / $total_tickets_avail) * 100;

            if ($device == "1") {
                // moble
                $width = "300";
                $w1 = "20";
                $w2 = "150";
                $w3 = "130";

            } else {
                // desktop
                $width = "500";
                $w1 = "50";
                $w2 = "150";
                $w3 = "130";

            }

			// New detailed report
			print "<table border=\"0\" width=\"$width\">
			<tr><td colspan=3><b>Tickets Sold</b></td></tr>

			<tr>
				<td width=\"$w1\">&nbsp;</td>
				<td width=\"$w2\"><b>Total</b></td>
				<td width=\"$w3\">&nbsp;</td>
			</tr>
			<tr valign=top>
				<td width=\"$w1\">&nbsp;</td>
				<td width=\"$w2\">

				<div class=\"progress\">
					<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"$per_sold\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $per_sold%;\">$per_sold%</div>
				</div>

				</td>
				<td width=\"$w3\">$total_tickets_sold/$total_tickets_avail</td>
			</tr>
			";
			$sql3 = "SELECT `id` FROM `tickets` WHERE `eventID` = '$_GET[id]' ORDER BY `name` ASC";
			$result3 = $this->new_mysql($sql3);
			while ($row3 = $result3->fetch_assoc()) {
				$html = $this->break_down($row3['id']);
				print "$html";
			}
			print "
			</table>";
			print "<hr>";
			print "<h1>Sales</h1>";



			// get total sales

                        $months = array("Jan","Feb","Mar","Arp","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

                        foreach ($months as $month) {
                                $sql2 = "
                                SELECT
                                        `cart`.`price`,
                                        `cart`.`qty`
                                FROM
                                        `cart`
                                WHERE `cart`.`eventID` = '$_GET[id]'
                                AND `cart`.`status` = 'Paid'
                                AND DATE_FORMAT(`cart`.`date`, '%b') = '$month'
                                ";
                                $result2 = $this->new_mysql($sql2);
				$found = "0";
				$temp = "0";
				$total_m = "0";
				while ($row2 = $result2->fetch_assoc()) {
					$found = "1";
					$temp = $row2['price'] * $row2['qty'];
					$total_m = $total_m + $temp;
				}
				if ($found == "0") {
					$total_m = "0";
				}
				$data[] = $total_m;

                        }

			$sql2 = "
			SELECT
				`cart`.`price`,
				`cart`.`qty`,
				DATE_FORMAT(`cart`.`date`, '%b') AS 'date'
			FROM
				`cart`

			WHERE
				`cart`.`eventID` = '$_GET[id]'
				AND `cart`.`status` = 'Paid'

			";



			$result2 = $this->new_mysql($sql2);
			while ($row2 = $result2->fetch_assoc()) {
				$total_line = $row2['price'] * $row2['qty'];
				$total = $total + $total_line;
				$total_qty = $total_qty + $row2['qty'];
				$month = $row2['date'];
				$date_month[$month] = $date_month[$month] + $row2['qty'];
			}
			//print "<table class=\"table\">
			//<tr><td>$".number_format($total)."</td><td>$total_qty</td></tr>
			//<tr><td><b>GROSS SALES</b></td><td><b>TICKETS SOLD</b></td></tr>
			//</table>";

			$img = $this->bar_graph($data,'Sales Summary','Dollars');
			print "$img";

			?>

			  <style>
			  .achievements-wrapper { height: 300px; overflow: auto; }
			  </style>

	                <div class="panel panel-default panel-table">
                        <div class="panel-heading">
                                <div class="title">Tickets Sales</div>
                        </div>
                        <div class="panel-body table-responsive">
                                <div class="span3 achievements-wrapper">
                                  <table class="table table-striped table-borderless">
                                    <thead>
                                      <tr>
					<th style="width:20%;">Name</th>
                                        <th style="width:20%;">Ticket</th>
                                        <th class="number">Price</th>
                                        <th style="width:20%;">Date</th>
                                        <th style="width:20%;">Status</th>
                                      </tr>
                                    </thead>
                                    <tbody class="no-border-x">
                                        <?=$html2;?>
                                    </tbody>
                                  </table>
                                </div>
                        </div>
        	        </div>
			<?php

		}

	}

   public function bar_graph($data,$title,$side_title) {
      $device = $this->device_type();

      if ($device == "1") {
        // mobile
        $width = "430";
        $height = "400";
      } else {
        // desktop
        $width = "800";
        $height = "400";
      }

      require_once ('jpgraph/src/jpgraph.php');
      require_once ('jpgraph/src/jpgraph_bar.php');


	//$data = array(2,5,3,8,9,2,4,7);

      // Create the graph. These two calls are always required
      $graph = new Graph($width,$height);
      $graph->SetScale('textlin');

      // Add a drop shadow
      $graph->SetShadow();

      // Adjust the margin a bit to make more room for titles
      $graph->SetMargin(80,140,20,40);

      // Create a bar pot
      $bplot = new BarPlot($data);
      $graph->Add($bplot);

      //$bplot->SetFillColor(array('purple','blue','green','orange','red'));
      //$graph->Add($bplot);


      // Setup the titles
      $graph->title->Set($title);
      $graph->yaxis->title->SetMargin(15);
      $graph->xaxis->title->Set('Month');
      $graph->yaxis->title->Set($side_title);
      $graph->xaxis->SetTickLabels(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'));
      if ($device == "1") {
        $graph->xaxis->SetLabelAngle(50);
      }

      $graph->title->SetFont(FF_FONT1,FS_BOLD);
      $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
      $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

      // Display the graph
      //$graph->Stroke();
      $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
      $rand = date("U");
      $rand .= rand(50,600);
      $fileName = ".output/$rand.png";
      $graph->img->Stream($fileName);
      $image = "<img src=\"$fileName\">";
      return $image;
   }


	public function details_template($form,$btn,$post,$step) {
        $device = $this->device_type();
        if ($device == "0") {
            $size = "40";
        } else {
            $device = "20";
        }

	$this->check_section('event_details');

		if ($step == "1") {

			print '
		        	<div class="page-head">
		        		<h2 class="page-head-title">New Event : Step 1 of 4</h2>
		        	</div>
			';

			print '
		        <div class="main-content container-fluid">
		          <div class="row wizard-row">
		            <div class="col-md-12 fuelux">
		              <div class="block-wizard panel panel-default">
		                <div id="wizard1" class="wizard wizard-ux">
		                  <ul class="steps">
		                    <li data-step="1" class="active">Step 1<span class="chevron"></span></li>
		                    <li data-step="2">Step 2<span class="chevron"></span></li>
		                    <li data-step="3">Step 3<span class="chevron"></span></li>
                                    <li data-step="4">Step 4<span class="chevron"></span></li>

		                  </ul>
			';

			/*
			print '
			<h2>New Event : Step 1 of 4</h2>
			<nav>
			  <ul class="pagination">
			    <li class="active"><a href="javascript:void(0)">1 <span class="sr-only">(current)</span></a></li>
			    <li class="disabled"><a href="javascript:void(0)">2 <span class="sr-only"></span></a></li>
			    <li class="disabled"><a href="javascript:void(0)">3 <span class="sr-only"></span></a></li>
			    <li class="disabled"><a href="javascript:void(0)">4 <span class="sr-only"></span></a></li>
			  </ul>
			</nav>
			';
			*/

		} else {
                                print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Event Details</h2></h2>
                                </div>
                                ';

			print "
			<nav>
				<ul class=\"pagination\">
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\">(current)</span></a></li>
				<li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=social&id=$_GET[id]\">Social <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

				</ul>
			</nav>
			";

		}


		$location = $this->get_locations($post['locationID']);
		$category = $this->get_category($post['categoryID']);
		$options = $this->get_templates($post['templateID']);
		$settings = $this->get_settings();

		if ($post['registration'] == "Yes") {
			$registration = "checked";
		}

		if ($post['notifications'] == "Yes") {
			$notifications = "checked";
		}

		print '
                <div class="step-content">
                	<div data-step="1" class="step-pane active">
		';

		print "
		<form action=\"index.php\" method=\"post\" class=\"form-horizontal group-border-dashed\">
		$form


                <div class=\"form-group\">
	                <label class=\"col-sm-3 control-label\">Title</label>
        	        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"title\" placeholder=\"Title\" value=\"$post[title]\" size=$size class=\"form-control\"> 
                	</div>
                </div>

                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Tagline</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"tagline\" value=\"$post[tagline]\" size=$size class=\"form-control\" placeholder=\"Tagline\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Desktop Template</label>
                        <div class=\"col-sm-6\">
				<select name=\"templateID\" class=\"form-control\">$options</select>
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Location</label>
                        <div class=\"col-sm-6\">
				<select name=\"locationID\" class=\"form-control\">$location</select>
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Event Address</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"address\" placeholder=\"Example: 67 South St, Raleigh, NC\" size=$size value=\"$post[address]\" class=\"form-control\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Category</label>
                        <div class=\"col-sm-6\">
				<select name=\"categoryID\" class=\"form-control\">$category</select>
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Start Date</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"start_date\" value=\"$post[start_date]\" id=\"start_date\" class=\"form-control\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">End Date</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"end_date\" value=\"$post[end_date]\" id=\"end_date\" class=\"form-control\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Start Time</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"start_time\" value=\"$post[start_time]\" id=\"start_time\" class=\"form-control\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">End Time</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"end_time\" id=\"end_time\" value=\"$post[end_time]\" class=\"form-control\">
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Notifications</label>
                        <div class=\"col-sm-6\">
				<input type=\"checkbox\" name=\"notifications\" value=\"Yes\" $notifications>&nbsp;<i>Alerts on payments and registration</i>
                        </div>
                </div>

                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Registration</label>
                        <div class=\"col-sm-6\">
				<input type=\"checkbox\" name=\"registration\" value=\"Yes\" $registration> <input type=\"button\" class=\"btn btn-success\" value=\"Manage Form\" onclick=\"window.open('index.php?section=dashboard&center=registration&id=$post[id]')\">&nbsp;<i>Please enable the Registration Desktop Theme</i>
                        </div>
                </div>

		";
                if ($post['registration'] == "Yes") {
                print "<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\">Registration iFrame Code for your website</label>
		<div class=\"col-sm-6\">
                <textarea cols=100 rows=5><iframe src=\"http://$settings[8]/registration.php?id=$post[id]\" style=\"border:0px #FFFFFF none;\" name=\"myiFrame\" scrolling=\"yes\" frameborder=\"0\" marginheight=\"0px\" marginwidth=\"0px\" height=\"500px\" width=\"700px\"></iframe>

                </textarea>
                </div></div>";
                }

		print "
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Description</label>
                        <div class=\"col-sm-6\">
				<textarea name=\"description\" id=\"tiny\" cols=60 rows=20>$post[description]</textarea>
                        </div>
                </div>


                <div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
                        <div class=\"col-sm-6\">
				<input type=\"submit\" value=\"$btn\" class=\"btn btn-primary\">
                        </div>
                </div>













		</div></div></div></div></div></div></div>


		";

		/*
		print "
		<table class=\"table\">
		<tr>
			<td width=\"200\">Title:</td>
			<td><input type=\"text\" name=\"title\" value=\"$post[title]\" size=$size></td>
		</tr>
		<tr>
			<td>Tagline:</td>
			<td><input type=\"text\" name=\"tagline\" value=\"$post[tagline]\" size=$size></td>
		</tr>
		<tr>
			<td>Desktop Template:</td>
			<td><select name=\"templateID\">$options</select></td>
		</tr>
		<tr>
			<td>Location:</td>
			<td><select name=\"locationID\">$location</select></td>
		</tr>
		<tr>
			<td>Event Address:</td>
			<td><input type=\"text\" name=\"address\" placeholder=\"Example: 67 South St, Raleigh, NC\" size=$size value=\"$post[address]\"></td>
		</tr>
		<tr>
			<td>Category:</td>
			<td><select name=\"categoryID\">$category</select></td>
		</tr>
		<tr>
			<td>Event Start Date:</td>
			<td><input type=\"text\" name=\"start_date\" value=\"$post[start_date]\" id=\"start_date\"></td>
		</tr>
		<tr>
			<td>Event End Date:</td>
			<td><input type=\"text\" name=\"end_date\" value=\"$post[end_date]\" id=\"end_date\"></td>
		</tr>
		<tr>
			<td>Event Start Time:</td>
			<td><input type=\"text\" name=\"start_time\" value=\"$post[start_time]\" id=\"start_time\"></td>
		</tr>
		<tr>
			<td>Event End Time:</td>
			<td><input type=\"text\" name=\"end_time\" id=\"end_time\" value=\"$post[end_time]\"></td>
		</tr>

		<tr>
			<td colspan=2>Enable Email Notifications<br><input type=\"checkbox\" name=\"notifications\" value=\"Yes\" $notifications>&nbsp;<i>Alerts on payments and registration</i></td>
		</tr>

		<tr>
			<td colspan=2>Enable Registration?<br><input type=\"checkbox\" name=\"registration\" value=\"Yes\" $registration> <input type=\"button\" class=\"btn btn-success\" value=\"Manage Form\" onclick=\"window.open('index.php?section=dashboard&center=registration&id=$post[id]')\">&nbsp;<i>Please enable the Registration Desktop Theme</i></td>
		</tr>
		";

		if ($post['registration'] == "Yes") {
		print "<tr><td colspan=2><b>Registration iFrame Code for your website:</b><br>
		<textarea cols=100 rows=5><iframe src=\"http://$settings[8]/registration.php?id=$post[id]\" style=\"border:0px #FFFFFF none;\" name=\"myiFrame\" scrolling=\"yes\" frameborder=\"0\" marginheight=\"0px\" marginwidth=\"0px\" height=\"500px\" width=\"700px\"></iframe>

		</textarea>
		</td></tr>";
		}


		print "
		<tr>
			<td colspan=2>Description:</td>
		</tr>
		<tr>
			<td colspan=2><textarea name=\"description\" id=\"tiny\" cols=60 rows=20>$post[description]</textarea></td>
		</tr>

		<!--
        <tr><td colspan=2>More Info:</td></tr>
		<tr><td colspan=2><textarea name=\"more_info\" id=\"tiny2\" cols=60 rows=20>$post[more_info]</textarea></td></tr>
        -->

		<tr><td colspan=2><input type=\"submit\" value=\"$btn\" class=\"btn btn-primary\"></td></tr>
		</table>
		</form>

		";
		*/


		?>

		<script>
                $(function() {
                    $('#start_time').timepicker();
                });

                $(function() {
                    $('#end_time').timepicker();
                });


		$(function() {
			$( "#start_date"   ).datepicker({
				dateFormat: "yy-mm-dd",
				changeMonth: true,
				changeYear: true
			});
			$( "#end_date"      ).datepicker({
				dateFormat: "yy-mm-dd",
				changeMonth: true,
				changeYear: true
			});
		});
		</script>

		<?php


	}

	public function save_details() {

                $this->navigation2();
		print '
		      <div class="be-content">
		        <div class="main-content container-fluid">
		          <div class="row">
		            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
				<div id="ajax">
		';

                //print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';

		$sql = "INSERT INTO `events` 
		(`userID`,`title`,`tagline`,`locationID`,`categoryID`,`start_date`,`end_date`,`start_time`,`end_time`,`description`,`address`,`templateID`,`more_info`,`registration`,`notifications`) VALUES 
		('$_SESSION[id]','$_POST[title]','$_POST[tagline]','$_POST[locationID]','$_POST[categoryID]','$_POST[start_date]','$_POST[end_date]','$_POST[start_time]','$_POST[end_time]','$_POST[description]','$_POST[address]','$_POST[templateID]',
		'$_POST[more_info]','$_POST[registration]','$_POST[notifications]')";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {

			$eventID = $this->linkID->insert_id;
			$_GET['id'] = $eventID;
			$_GET['step'] = "2";
			$this->edit_design();

			//print "<font color=green>The event was created.</font> <br>";
			//$this->details();
		} else {
			print "<br><br><font color=red>There was an error creating your event.</font><br><br>";
		}

		print "</div></div></div></div>";
	}

	public function update_details() {
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
                                <div id="ajax">
                ';

		$sql = "UPDATE `events` SET `title` = '$_POST[title]', `tagline` = '$_POST[tagline]', `locationID` = '$_POST[locationID]', `categoryID` = '$_POST[categoryID]', `start_date` = '$_POST[start_date]',
		`end_date` = '$_POST[end_date]', `start_time` = '$_POST[start_time]', `end_time` = '$_POST[end_time]', `description` = '$_POST[description]' , `address` = '$_POST[address]', `templateID` = '$_POST[templateID]',
		`more_info` = '$_POST[more_info]', `registration` = '$_POST[registration]', `notifications` = '$_POST[notifications]'
		WHERE `id` = '$_POST[id]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<font color=green>The event was updated.</font> <br>";
                        $this->details();
                } else {
                        print "<br><br><font color=red>There was an error updating your event.</font><br><br>";
                }

		print "</div></div></div></div>";

	}

	public function delete_details() {
		$sql = "DELETE FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        print "<font color=green>The event was deleted.</font> <br>";
                        $this->details();
                } else {
                        print "<br><br><font color=red>There was an error deleting your event.</font><br><br>";
                }



	}

	public function check_section($part) {
		if ($_SESSION['resellerID'] == "0") {
		    $event_details = "Yes";
		    $_SESSION['event_details'] = $event_details;
		    $event_design = "Yes";
		    $social = "Yes";
		    $event_settings = "Yes";
		    $create_tickets = "Yes";
		    $checkin = "Yes";
		} else {
		    $event_details = $_SESSION['event_details'];
		    $event_design = $_SESSION['event_design'];
		    $social = $_SESSION['social'];
		    $event_settings = $_SESSION['event_settings'];
		    $create_tickets = $_SESSION['create_tickets'];
		    $_SESSION['id'] = $_SESSION['resellerID'];
		}

		switch ($part) {
			case "event_details":
			if ($event_details != "Yes") {
				$this->sorry_die('Details');
			}
			break;

			case "event_design":
			if ($event_design != "Yes") {
				$this->sorry_die('Design');
			}
			break;

			case "social":
			if ($social != "Yes") {
				$this->sorry_die('Social');
			}
			break;

			case "event_settings":
			if ($event_settings != "Yes") {
				$this->sorry_die('Settings');
			}
			break;

			case "create_tickets":
			if ($create_tickets != "Yes") {
				$this->sorry_die('Tickets');
			}
			break;
		}
	}

	private function sorry_die($section) {
		print "<br><br><font color=red>Sorry, but you do not have access to <b>$section</b></font><br><br>";
		die;
	}

	public function design() {
        $device = $this->device_type();

		print "<h2>Event Design</h2>";

                $sql = "
                SELECT
                        `l`.`location`,
                        `c`.`category`,
                        `e`.`title`,
                        `e`.`id`,
			`e`.`cover_image`,
			`e`.`slide1`,
			`e`.`video`,
                        DATE_FORMAT(`e`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`e`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `events` e,`location` l, `category` c

                WHERE
                        `e`.`locationID` = `l`.`id`
                        AND `e`.`categoryID` = `c`.`id`
                        AND `e`.`userID` = '$_SESSION[id]'
                ";

                print "<table class=\"table\">
                <tr>
                        <td width=200><b>Title</b></td>
                        <td width=50><b>Image</b></td>
                        <td width=50><b>Slideshow</b></td>
                        <td width=50><b>Video</b></td>
                        <td width=100>&nbsp;</td>
                </tr>";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {

			$image = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" title=\"The cover image or flyer is missing\"><font color=#E68A1A><span class=\"glyphicon glyphicon-flag\"></span></font></a>";
			$slideshow = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" title=\"The slideshow is missing\"><font color=#E68A1A><span class=\"glyphicon glyphicon-flag\"></span></font></a>";
			$video = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" title=\"The video is missing\"><font color=#E68A1A><span class=\"glyphicon glyphicon-flag\"></span></font></a>";

			if ($row['cover_image'] != "") {
				$image = "<font color=\"#21610B\"><span class=\"glyphicon glyphicon-ok\"></span></font>";
			}
			if ($row['slide1'] != "") {
				$slideshow = "<font color=\"#21610B\"><span class=\"glyphicon glyphicon-ok\"></span></font>";
			}
			if ($row['video'] != "") {
				$video = "<font color=\"#21610B\"><span class=\"glyphicon glyphicon-ok\"></span></font>";
			}


                        print "<tr><td>$row[title]</td>
			<td>$image</td>
			<td>$slideshow</td>
			<td>$video</td>
                        <td>
                                <input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='index.php?section=dashboard&center=edit_design&id=$row[id]'\">
                        </td></tr>";
                        $found = "1";
                }
                if ($found != "1") {
                        print "<tr><td colspan=6>Sorry, there are no events. Please add one.</td></tr>";
                }
                print "</table>";

	}


	public function edit_design() {
        $device = $this->device_type();

		$this->check_section('event_design');

		if ($_GET['step'] == "2") {

                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">New Event : Step 2 of 4</h2>
                                </div>
                        ';

                        print '
                        <div class="main-content container-fluid">
                          <div class="row wizard-row">
                            <div class="col-md-12 fuelux">
                              <div class="block-wizard panel panel-default">
                                <div id="wizard1" class="wizard wizard-ux">
                                  <ul class="steps">
                                    <li data-step="1">Step 1<span class="chevron"></span></li>
                                    <li data-step="2" class="active">Step 2<span class="chevron"></span></li>
                                    <li data-step="3">Step 3<span class="chevron"></span></li>
                                    <li data-step="4">Step 4<span class="chevron"></span></li>

                                  </ul>
                        ';


		} else {
                                print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Edit Design</h2></h2>
                                </div>
                                ';

                        print "
                        <nav>
                                <ul class=\"pagination\">
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\">(current)</span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=social&id=$_GET[id]\">Social <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

                                </ul>
                        </nav>
                        ";
		}

		$sql = "
		SELECT 
			`events`.*,
			`templates`.`cover_photo_dimentions` 

		FROM `events`,`templates`

		WHERE 
			`events`.`id` = '$_GET[id]' 
			AND `events`.`userID` = '$_SESSION[id]'
			AND `events`.`templateID` = `templates`.`id`
		";

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {

			if ($row['cover_image'] != "") {
				$cover = "
				<a href=\"uploads/".$_SESSION['id']."/cover/".$_GET['id']."/".$row['cover_image']."\" target=_blank>
				<img src=\"uploads/".$_SESSION['id']."/cover/".$_GET['id']."/".$row['cover_image']."\" width=200></a>";
			} else {
				$cover = "Please upload a cover photo";
			}

			print "
			<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal group-border-dashed\">
			<input type=\"hidden\" name=\"section\" value=\"update_design\">
			<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
			";


			if ($_GET['step'] == "2") {
				print "<input type=\"hidden\" name=\"step\" value=\"3\">";
			}


                print '
                <div class="step-content">
                        <div data-step="1" class="step-pane active">
                ';

		print "
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Cover Image or Flyer</label>
                        <div class=\"col-sm-6\">
			$cover<br>
			<input type=\"file\" name=\"cover_image\" class=\"form-control\"><br>Please size image as <b>$row[cover_photo_dimentions]</b>
                        </div>
                </div>


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Slideshow</label>
		</div>

		";
			for ($x=1; $x < 6; $x++) {
				$img = "slide";
				$img .= $x;

				if ($row[$img] != "") {
	                                $slide = "
        	                        <a href=\"uploads/".$_SESSION['id']."/slide/".$_GET['id']."/".$row[$img]."\" target=_blank>
                	                <img src=\"uploads/".$_SESSION['id']."/slide/".$_GET['id']."/".$row[$img]."\" width=200></a>";
                                    if ($device == "1") {
                                        $slide .= "<br>";
                                    }
				} else {
					$slide = "Please upload a photo";
				}

				print "<div class=\"form-group\">
				<label class=\"col-sm-3 control-label\">Image $x $slide</label>
				<div class=\"col-sm-6\"><input type=\"file\" name=\"$img\" class=\"form-control\"></div>
				</div>";
				}

		if ($_GET['step'] == "2") {

			print "<div class=\"form-group\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Next Step\"></div>";

		} else {
			print "<div class=\"form-group\"><label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></div></div>";
		}
			print "
			</form>";
			print "</div></div></div></div></div></div></div>";
		}
	}


	public function update_design() {
		// init
		$ok = "0";
		$fail = "0";

		// get the directory ready, if file exists the @ will ignore.
		$dir = "uploads/$_SESSION[id]";
		@mkdir($dir);
		@chmod($dir,0755);
		$cover = $dir . "/cover";
		$slide = $dir . "/slide";

                @mkdir($cover);
                @chmod($cover,0755);

                @mkdir($slide);
                @chmod($slide,0755);

		$cover2 = $cover . "/$_POST[id]";
		$slide2 = $slide . "/$_POST[id]";

                @mkdir($cover2);
                @chmod($cover2,0755);

                @mkdir($slide2);
                @chmod($slide2,0755);

		// upload files


		// cover image
                $fileName = $_FILES['cover_image']['name'];
                $tmpName  = $_FILES['cover_image']['tmp_name'];
                $fileSize = $_FILES['cover_image']['size'];
                $fileType = $_FILES['cover_image']['type'];
		if ($fileName != "") {
			$ext = $this->file_types($fileType);
                        if ($ext == "1") {
				print "Error: cover photo:<br>";
                                print "Supported file types are<br>GIF, PNG, JPG<br>";
                                print "To have your file type added please email the administrator this code: $fileType<br>";
                        } else {
				move_uploaded_file("$tmpName", "$cover2/$fileName");
				chmod("$cover2/$fileName", 0644);
				$sql = "UPDATE `events` SET `cover_image` = '$fileName', `cover_meta` = '$fileType' WHERE `id` = '$_POST[id]' AND `userID` = '$_SESSION[id]'";
				$result = $this->new_mysql($sql);
				if ($result == "TRUE") {
					$ok++;
				} else {
					$fail++;
				}
			}
		}
		// slide image
                for ($x=1; $x < 6; $x++) {
	                $img = "slide";
                        $img .= $x;
			$meta = "slide";
			$meta .= $x;
			$meta .= "_meta";
	                $fileName = $_FILES[$img]['name'];
        	        $tmpName  = $_FILES[$img]['tmp_name'];
	                $fileSize = $_FILES[$img]['size'];
        	        $fileType = $_FILES[$img]['type'];
			if ($fileName != "") {
				$ext = $this->file_types($fileType);
				if ($ext == "1") {
                                	print "Error: slideshow image $x:<br>";
	                                print "Supported file types are<br>GIF, PNG, JPG<br>";
        	                        print "To have your file type added please email the administrator this code: $fileType<br>";
                	        } else {
	                                move_uploaded_file("$tmpName", "$slide2/$fileName");
        	                        chmod("$slide2/$fileName", 0644);
                	                $sql = "UPDATE `events` SET `$img` = '$fileName', `$meta` = '$fileType' WHERE `id` = '$_POST[id]' AND `userID` = '$_SESSION[id]'";
                        	        $result = $this->new_mysql($sql);
                                	if ($result == "TRUE") {
                                        	$ok++;
	                                } else {
        	                                $fail++;
                	                }
				}
			}
		}

		// video
		if ($_POST['video'] != "") {
			$sql = "UPDATE `events` SET `video` = '$_POST[video]' WHERE `id` = '$_POST[id]' AND `userID` = '$_SESSION[id]'";
			$result = $this->new_mysql($sql);
		}

	

                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
                                <div id="ajax">
                ';

			if ($_POST['step'] == "3") {
				$_GET['id'] = $_POST['id'];
				$_GET['step'] = "3";
				$this->edit_settings();
			} else {
				print "A total of $ok images was uploaded with no errors and $fail failed to upload.<br>";
				$this->design();
			}


		print "</div></div></div></div>";


	}


        public function file_types($type) {
                $ext = "1";
                $sql = "SELECT * FROM `file_types` WHERE `meta` = '$type'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $ext = $row['ext'];
                }
                return $ext;
        }


	public function event_settings() {
                $srv_settings = $this->get_settings();

		$this->check_section('event_settings');

		print "<h2>Event Settings</h2>";

                $sql = "
                SELECT
                        `l`.`location`,
                        `c`.`category`,
                        `e`.`title`,
                        `e`.`id`,
                        `e`.`cover_image`,
                        `e`.`slide1`,
                        `e`.`video`,
			`e`.`event_page`,
			`e`.`homepage`,
                        DATE_FORMAT(`e`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`e`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `events` e,`location` l, `category` c

                WHERE
                        `e`.`locationID` = `l`.`id`
                        AND `e`.`categoryID` = `c`.`id`
                        AND `e`.`userID` = '$_SESSION[id]'
                ";

                print "<table class=\"table\">
                <tr>
                        <td width=200><b>Title</b></td>
                        <td width=75><b>Event Page</b></td>
                        <td width=75><b>Homepage</b></td>
                        <td width=100>&nbsp;</td>
                </tr>";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {

                        $event_page = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" title=\"The event page needs to be set either public or private\"><font color=#E68A1A><span class=\"glyphicon glyphicon-flag\"></span></font></a>";
                        $homepage = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" title=\"The homepage is missing\"><font color=#E68A1A><span class=\"glyphicon glyphicon-flag\"></span></font></a>";

                        if ($row['event_page'] != "") {
                                $event_page = "<font color=\"#21610B\"><span class=\"glyphicon glyphicon-ok\"></span></font>";
                        }
                        if ($row['homepage'] != "") {
				$domain = $row['homepage'] . "." . $srv_settings[8];
                                $homepage = "<a href=\"http://$domain\" target=_blank><font color=\"#21610B\"><span class=\"glyphicon glyphicon-ok\"></span></font></a>";
                        }


                        print "<tr><td>$row[title]</td>
                        <td>$event_page</td>
                        <td>$homepage</td>
                        <td>
                                <input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" onclick=\"document.location.href='index.php?section=dashboard&center=edit_settings&id=$row[id]'\">
                        </td></tr>";
                        $found = "1";
                }
                if ($found != "1") {
                        print "<tr><td colspan=6>Sorry, there are no events. Please add one.</td></tr>";
                }
                print "</table>";


	}

	public function edit_settings() {
                $srv_settings = $this->get_settings();

		if ($_GET['step'] == "3") {

                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">New Event : Step 3 of 4</h2>
                                </div>
                        ';

                        print '
                        <div class="main-content container-fluid">
                          <div class="row wizard-row">
                            <div class="col-md-12 fuelux">
                              <div class="block-wizard panel panel-default">
                                <div id="wizard1" class="wizard wizard-ux">
                                  <ul class="steps">
                                    <li data-step="1">Step 1<span class="chevron"></span></li>
                                    <li data-step="2">Step 2<span class="chevron"></span></li>
                                    <li data-step="3" class="active">Step 3<span class="chevron"></span></li>
                                    <li data-step="4">Step 4<span class="chevron"></span></li>

                                  </ul>
                        ';


		} else {
                                print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Event Settings</h2></h2>
                                </div>
                                ';

                        print "
                        <nav>
                                <ul class=\"pagination\">
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\">(current)</span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=social&id=$_GET[id]\">Social <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

                                </ul>
                        </nav>
                        ";
		}

		$sql = "SELECT * FROM `events` WHERE `userID` = '$_SESSION[id]' AND `id` = '$_GET[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			if ($row['event_page'] == "public") {
				$public = "checked";
			}
			if ($row['event_page'] == "private") {
				$private = "checked";
			}
			//print " ($row[title])</h2>";


	                print '
        	        <div class="step-content">
	                        <div data-step="1" class="step-pane active">
        	        ';

			print "
			<form action=\"index.php\" method=\"post\" name=\"myform\" class=\"form-horizontal group-border-dashed\">
			<input type=\"hidden\" name=\"section\" value=\"update_settings\">
			<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
			<input type=\"hidden\" name=\"step\" value=\"4\">

                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Event Page</label>
                        <div class=\"col-sm-6\">
				<input type=\"radio\" name=\"event_page\" value=\"public\" $public> Public<br>
                                <input type=\"radio\" name=\"event_page\" value=\"private\" $private> Private<br>
                                <i>* Private events can not be searched for though Ticket Pointe</i>
                        </div>
                </div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Homepage</label>
			<div class=\"col-sm-6\">
				<input type=\"text\" value=\"$row[homepage]\" name=\"homepage\" id=\"homepage\" 
				onblur=\"check_dns(this.form)\">.$srv_settings[8]
				<br><div id=\"dns\" style=\"display:inline\"></div>
			</div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"submit\" value=\"Save\" class=\"btn btn-primary\"></div>
		</div>


			</form>
			";
			?>
			<script>

			$('input').keyup(function() {
			    str = $(this).val();
			    str = str.replace(/\s/g,'');
			    $(this).val(str);
			});

			function check_dns(myform) {
			        $.get('ajax/checkdns.php',
			        $(myform).serialize(),
			        function(php_msg) {
			                $("#dns").html(php_msg);
			        });
			}
			</script>

			<?php
		}

	}

	public function update_settings() {
		$srv_settings = $this->get_settings();
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
                                <div id="ajax">
                ';

		$found_owner = "0";
		$sql = "SELECT * FROM `parked_domains` WHERE `parked_domain` = '$_POST[homepage]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			if ($row['userID'] == $_SESSION['id']) {
				$found_owner = "1";
			} else {
				$found_owner = "2";
			}
		}

		if ($found_owner == "2") {
			// can't use that name or blank it out
			$_POST['homepage'] = "";
		}
                include_once 'class/xmlapi.php';
		// CHANGE To Blurstorm's IP
                $server_ip = $srv_settings[5];
                $xmlapi = new xmlapi($server_ip);
		// Update password
                $domain_user = $srv_settings[6];
                $domain_pw = $srv_settings[7];


                # switch to cPanel
                $xmlapi->set_debug(1);
                $xmlapi->set_output('json');
                $xmlapi->set_port(2083);
                $xmlapi->password_auth($domain_user,$domain_pw);

                //$result = $xmlapi->api1_query( $domain_user, 'Mysql', 'adddb', array('test'));
		if (($_POST['homepage'] != "") && ($found_owner == "0")) {
			$domain = $_POST['homepage'];
			$domain .= ".";
			$domain .= $srv_settings[8];  // UPDATE THE DOMAIN
	                $result = $xmlapi->api1_query( $domain_user, 'Park', 'park', array($domain));
        	        $json_data = json_decode($result);

                	$new_array = $this->objectToArray($json_data);

	                $new_result = $new_array['data']['result'];
        	        if (preg_match('/successfully/i', $new_result)) {
                	        //print "The domain was created.\n";
				$sql2 = "INSERT INTO `parked_domains` (`userID`,`parked_domain`) VALUES ('$_SESSION[id]','$_POST[homepage]')";
				$result2 = $this->new_mysql($sql2);
				//print "<br><font color=green>The domain was created sucessfully.<br></font>";
	                } else {
        	                print "<br><font color=red>The domain failed to be created.</font><br>\n";
				print "<pre>";
				print_r($json_data);
				print "</pre>";

	                }
		}
                $sql = "UPDATE `events` SET `event_page` = '$_POST[event_page]', `homepage` = '$_POST[homepage]' WHERE `userID` = '$_SESSION[id]' AND `id` = '$_POST[id]'";
		$result = $this->new_mysql($sql);

		if ($result == "TRUE") {
			if ($_POST['step'] == "4") {
				//

			} else {
				print "The event settings was saved.<br>";
			}
		} else {
			print "The event settings failed to update.<br>";
		}

		if ($_POST['step'] == "4") {
                                $_GET['id'] = $_POST['id'];
                                $_GET['step'] = "4";
                                $this->manage_tickets();
		} else {
			$this->event_settings();
		}

		print "</div></div></div></div>";
	}

	public function edit_tickets() {
        $device = $this->device_type();
                $sql = "SELECT * FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $title = $row['title'];
                }

		if ($_GET['step'] == "4") {
                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">New Event : Step 4 of 4</h2>
                                </div>
                        ';

                        print '
                        <div class="main-content container-fluid">
                          <div class="row wizard-row">
                            <div class="col-md-12 fuelux">
                              <div class="block-wizard panel panel-default">
                                <div id="wizard1" class="wizard wizard-ux">
                                  <ul class="steps">
                                    <li data-step="1">Step 1<span class="chevron"></span></li>
                                    <li data-step="2">Step 2<span class="chevron"></span></li>
                                    <li data-step="3">Step 3<span class="chevron"></span></li>
                                    <li data-step="4" class="active">Step 4<span class="chevron"></span></li>

                                  </ul>
                        ';
		} else {
                                print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Edit Tickets<br>'.$title.'</h2></h2>
                                </div>
                                ';

		}

		$sql = "SELECT * FROM `tickets` WHERE `id` = '$_GET[item]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
		$row = $result->fetch_assoc();

                print "
                <form name=\"myform\" action=\"index.php\" method=\"get\" class=\"form-horizontal group-border-dashed\">
                <input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
		<input type=\"hidden\" name=\"item\" value=\"$_GET[item]\">
		<input type=\"hidden\" name=\"section\" value=\"dashboard\">
		<input type=\"hidden\" name=\"center\" value=\"manage_tickets\">
		<input type=\"hidden\" name=\"up\" value=\"y\">


		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Ticket Name</label>
			<div class=\"col-sm-6\"><input type=\"text\" name=\"name\" id=\"name\" value=\"$row[name]\" class=\"form-control\"></div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">More Info</label>
			<div class=\"col-sm-6\"><input type=\"text\" name=\"more_info\" id=\"more_info\" value=\"$row[more_info]\" class=\"form-control\"></div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Quantity</label>
			<div class=\"col-sm-6\"><input type=\"text\" name=\"qty\" id=\"qty\" value=\"$row[qty]\" class=\"form-control\" onkeypress=\"validate(event)\"></div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Price</label>
			<div class=\"col-sm-6\"><input tpye=\"text\" name=\"price\" id=\"price\" value=\"$row[price]\" class=\"form-control\" onkeypress=\"return isNumberKey(event)\"></div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"submit\" value=\"Update Tickets\" class=\"btn btn-primary\"></div>
		</div>

                </form>
		";

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


	public function check_tax_id() {
		$sql = "SELECT `tax_id` FROM `users` WHERE `id` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$tax_id = $row['tax_id'];
		}	
		$d_tax_id = $this->encrypt_decrypt('decrypt',$tax_id);
		return $d_tax_id;

	}

	public function manage_tickets() {
        $device = $this->device_type();

		$this->check_section('create_tickets');


		$check_tax = $this->check_tax_id();
		if ($check_tax == "") {
			print "<br><br><font color=red>Sorry, but before you can create any tickets and or donations you must provide your tax ID.<br><br>The main business owner needs to add their tax ID in their profile.</font><br><br>";
			die;
		}

		$settings = $this->get_settings();
		$sql = "SELECT * FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$title = $row['title'];
			$enable_donation = $row['enable_donation'];
			$donation_goal = $row['donation_goal'];
		}

		if ($_GET['part'] == "donate") {
			if ($_GET['enable-donation'] == "checked") {
				$d = "Yes";
			} else {
				$d = "No";
			}
			$sql = "UPDATE `events` SET `enable_donation` = '$d', `donation_goal` = '$_GET[donation_goal]' WHERE `id` = '$_GET[id]' AND `events`.`userID` = '$_SESSION[id]'";
			$result = $this->new_mysql($sql);

		}

		if ($enable_donation == "No") {
			if ($_GET['step'] == "4") {
                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">New Event : Step 4 of 4</h2>
                                </div>
                        ';

                        print '
                        <div class="main-content container-fluid">
                          <div class="row wizard-row">
                            <div class="col-md-12 fuelux">
                              <div class="block-wizard panel panel-default">
                                <div id="wizard1" class="wizard wizard-ux">
                                  <ul class="steps">
                                    <li data-step="1">Step 1<span class="chevron"></span></li>
                                    <li data-step="2">Step 2<span class="chevron"></span></li>
                                    <li data-step="3">Step 3<span class="chevron"></span></li>
                                    <li data-step="4" class="active">Step 4<span class="chevron"></span></li>

                                  </ul>
                        ';
			} else {
	                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Manage Tickets <br>('.$title.')</h2></h2>
                                </div>
        	                ';

                        print "
			<div class=\"form-group\">
                        <nav>
                                <ul class=\"pagination\">
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\">(current)</span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=social&id=$_GET[id]\">Social <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

                                </ul>
                        </nav>
			</div>
                        ";
			}
	

                print '
                <div class="step-content">
                        <div data-step="1" class="step-pane active">
                ';

		print "
		<form name=\"myform\" class=\"form-horizontal group-border-dashed\">
		<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Ticket Name</label>
                        <div class=\"col-sm-6\">
				<input type=\"text\" name=\"name\" id=\"name\" class=\"form-control\">
                        </div>
                </div>


		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">More Info</label>
			<div class=\"col-sm-6\">
				<input type=\"text\" name=\"more_info\" class=\"form-control\">
			</div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Quantity</label>
			<div class=\"col-sm-6\">
				<input type=\"text\" name=\"qty\" id=\"qty\" onkeypress=\"validate(event)\" class=\"form-control\">
			</div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Price</label>
			<div class=\"col-sm-6\">
				<input tpye=\"text\" name=\"price\" id=\"price\" class=\"form-control\" onkeypress=\"return isNumberKey(event)\">
			</div>
		</div>


		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"button\" value=\"Add Tickets\" class=\"btn btn-primary\" onclick=\"add_tickets(this.form)\"></div>
		</div>

		</form>



		<div id=\"ticket_list\">
		<div class=\"form-group\">
			<label class=\"col-sm-2 control-label\">Name</label>
			<label class=\"col-sm-2 control-label\">Quantity</label>
			<label class=\"col-sm-2 control-label\">Price</label>
			<label class=\"col-sm-2 control-label\"></label>
		</div>

		";

		if ($_GET['up'] == "y") {
			$sql = "UPDATE `tickets` SET `name` = '$_GET[name]', `qty` = '$_GET[qty]', `price` = '$_GET[price]', `more_info` = '$_GET[more_info]' WHERE `id` = '$_GET[item]' AND `userID` = '$_SESSION[id]'";
                        $result = $this->new_mysql($sql);
		}

		if ($_GET['delete'] == "y") {
			$sql = "DELETE FROM `tickets` WHERE `eventID` = '$_GET[id]' AND `id` = '$_GET[item]' AND `userID` = '$_SESSION[id]'";
	                $result = $this->new_mysql($sql);
		}

		$sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_GET[id]' AND `userID` = '$_SESSION[id]' ORDER BY `name` ASC";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
                if (strlen($row['name']) > 20) {
                    $row['name'] = substr($row['name'], 0,20);
                    $row['name'] .= " ...";
                }

		print "
		<br><br>
                <div class=\"form-group\">
                        <div class=\"col-sm-2\">$row[name]</div>
			<div class=\"col-sm-2\">$row[qty]</div>
			<div class=\"col-sm-2\">$row[price]</div>
			<div class=\"col-sm-2\">
				<input type=\"button\" class=\"btn btn-primary\" value=\"Edit\" 
				onclick=\"document.location.href='index.php?section=dashboard&center=edit_tickets&id=$_GET[id]&item=$row[id]'\">
                    		<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" 
				onclick=\"if(confirm('WARNING: You are about to delete $row[name]')){
				document.location.href='index.php?section=dashboard&center=manage_tickets&id=$_GET[id]&delete=y&item=$row[id]'};\">
			</div>
		</div>";

		$found = "1";
		}
		if ($found != "1") {
			print "
			<div class=\"form-group\">
				<div col-sm-8>No results</div>
			</div>";
		}


	      print "
		<br><br>
		<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\"><b>Embed Payment for your web site</b></label>
		<div class=\"col-sm-6\">
	      Copy and paste the code below into your website.<br>
	      <textarea cols=60 rows=5><iframe src=\"https://www.$settings[8]/tickets_iframe.php?id=$_GET[id]\" style=\"border:0px #FFFFFF none;\" name=\"myiFrame\" scrolling=\"yes\" frameborder=\"0\" marginheight=\"0px\" marginwidth=\"0px\" height=\"700px\" width=\"900px\"></iframe>
                </textarea><br><br>
		</div>
		</div>
                ";


		} 

/*
		print "<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\"><b>Enable Donation</b></label>
		</div>";

		if ($enable_donation == "Yes") {
			$c1 = "checked";
		}


		if ($_SESSION['account_type'] == "2") {
			print "When donations are enabled tickets will not be available for purchase. Instead donations will be taken allowing the visitor to select a donation amount of their own amount.<br>
			<form name=\"myform2\" action=\"index.php\" method=\"get\">
                	<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
			<input type=\"hidden\" name=\"section\" value=\"dashboard\">
			<input type=\"hidden\" name=\"center\" value=\"manage_tickets\">
			<input type=\"hidden\" name=\"part\" value=\"donate\">

			<input type=\"checkbox\" name=\"enable-donation\" value=\"checked\" $c1> Enable Donations
			<br>Donation Goal Amount: <br>$<input type=\"text\" name=\"donation_goal\" value=\"$donation_goal\" size=20> <input type=\"submit\" value=\"Save\" class=\"btn btn-primary\">
			</form>
			";
		}
*/

		print "<br><br><br>";

		?>
		<script>

		function add_tickets(myform) {
		        $.get('ajax/add_tickets.php',
		        $(myform).serialize(),
		        function(php_msg) {
		                $("#ticket_list").html(php_msg);
		        });
                        document.getElementById('name').value='';
                        document.getElementById('qty').value='';
                        document.getElementById('price').value='';

		}



		function validate(evt) {
		  var theEvent = evt || window.event;
		  var key = theEvent.keyCode || theEvent.which;
		  key = String.fromCharCode( key );
		  var regex = /[0-9]|\./;
		  if( !regex.test(key) ) {
		    theEvent.returnValue = false;
		    if(theEvent.preventDefault) theEvent.preventDefault();
		  }
		}

	       function isNumberKey(evt)
	       {
        	  var charCode = (evt.which) ? evt.which : evt.keyCode;
	          if (charCode != 46 && charCode > 31 
        	    && (charCode < 48 || charCode > 57))
	             return false;

	          return true;
	       }
		</script>
		<?php


	}

	public function tickets() {
                print "<h2>Tickets</h2>";

		$today = date("Y-m-d");

                $sql = "
                SELECT
                        `l`.`location`,
                        `c`.`category`,
                        `e`.`title`,
                        `e`.`id`,
                        `e`.`cover_image`,
                        `e`.`slide1`,
                        `e`.`video`,
                        `e`.`event_page`,
                        `e`.`homepage`,
			`e`.`enable_donation`,
			DATEDIFF(`e`.`end_date`,'$today') AS 'date_diff',
                        DATE_FORMAT(`e`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`e`.`end_date`, '%m/%d/%Y') AS 'end_date'

                FROM
                        `events` e,`location` l, `category` c

                WHERE
                        `e`.`locationID` = `l`.`id`
                        AND `e`.`categoryID` = `c`.`id`
                        AND `e`.`userID` = '$_SESSION[id]'
                ";

                print "<table class=\"table\">
                <tr>
                        <td width=200><b>Title</b></td>
                        <td width=700>&nbsp;</td>
                </tr>";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {



			print "<tr><td>$row[title]</td>
			<td><input type=\"button\" class=\"btn btn-primary\" value=\"Manage Tickets\" onclick=\"document.location.href='index.php?section=dashboard&center=manage_tickets&id=$row[id]'\"> ";
			if ($row['enable_donation'] == "No") {
                                print "<input type=\"button\" class=\"btn btn-primary\" value=\"Manage Discounts\" onclick=\"document.location.href='index.php?section=dashboard&center=discounts&id=$row[id]'\">&nbsp;";
				print "<input type=\"button\" class=\"btn btn-success\" value=\"Download Ticket List\" onclick=\"window.open('download.php?id=$row[id]')\">&nbsp;";
			}

			if ($row['enable_donation'] == "Yes") {
				print "<input type=\"button\" class=\"btn btn-success\" value=\"Download Donation List\" onclick=\"window.open('download_donation.php?id=$row[id]')\">&nbsp;";
			}

			if ($row['date_diff'] < 15) {
				print "<input type=\"button\" class=\"btn btn-primary\" value=\"Request Payout\" onclick=\"document.location.href='index.php?section=dashboard&center=payout&id=$row[id]'\">";
			}

			print "</td>
			</tr>";

		}
		print "</table>";


	}

	public function discounts() {

                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">Discounts</h2>
                                </div>
                        ';


                        print "
                        <nav>
                                <ul class=\"pagination\">
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\">(current)</span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=social&id=$_GET[id]\">Social <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

                                </ul>
                        </nav>
                        ";
		if ($_GET['act'] == "del") {
			$sql = "DELETE FROM `discounts` WHERE `id` = '$_GET[id2]' AND `userID` = '$_SESSION[id]'";
                        $result = $this->new_mysql($sql);
		}

		if ($_GET['act'] == "new") {
			$sql = "INSERT INTO `discounts` (`eventID`,`userID`,`code`,`expire`,`amount_how`,`amount_off`) VALUES ('$_GET[id]','$_SESSION[id]','$_GET[discount]','$_GET[expire]','$_GET[amount_how]','$_GET[amount_off]')";
			$result = $this->new_mysql($sql);
		}


		$sql = "SELECT `title` FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
			$title = $row['title'];
		}

		print "
                <div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Event</label>
			<div class=\"col-sm-6\">$title</div>
		</div>
		";


		print "<form name=\"myform2\" action=\"index.php\" method=\"get\" class=\"form-horizontal group-border-dashed\">
		<input type=\"hidden\" name=\"section\" value=\"dashboard\">
		<input type=\"hidden\" name=\"center\" value=\"discounts\">
		<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
		<input type=\"hidden\" name=\"act\" value=\"new\">


                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\">Discount Code</label>
                        <div class=\"col-sm-6\"><input type=\"text\" name=\"discount\" placeholder=\"Please type in your discount code\" class=\"form-control\"></div>
                </div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Expiration Date</label>
			<div class=\"col-sm-6\"><input type=\"text\" name=\"expire\" id=\"start_date\" class=\"form-control\"></div>
		</div>


                <div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Discount</label>
			<div class=\"col-sm-6\">
			<select name=\"amount_how\"><option value=\"percent\">Percent Off</option><option value=\"dollar\">Dollar Amount</option></select>
			</div>
		</div>

		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\">Amount Off</label>
			<div class=\"col-sm-6\"><input type=\"text\" name=\"amount_off\"></div>
		</div>


		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Add Discount Code\"></div>
		</div>


		</form>
		";


                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">Existing Discounts</h2>
                                </div>
                        ';




		print "<div class=\"form-group\">
		<div class=\"col-sm-2\">Discount Code</div>
		<div class=\"col-sm-2\">Expiration Date</div>
		<div class=\"col-sm-2\">Type</div>
		<div class=\"col-sm-2\">Amount</div>
		<div class=\"col-sm-2\">&nbsp;</div>
		</div>
		";



		$sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {

			print "<br><br>
			<div class=\"form-group\">
			<div class=\"col-sm-2\">$row[code]</div>
			<div class=\"col-sm-2\">$row[expire]</div>
			<div class=\"col-sm-2\">$row[amount_how]</div>
			<div class=\"col-sm-2\">$row[amount_off]</div>
			<div class=\"col-sm-2\">
			<input type=\"button\" class=\"btn btn-warning\" value=\"Delete\" 
			onclick=\"document.location.href='index.php?section=dashboard&center=discounts&id=$_GET[id]&id2=$row[id]&act=del'\">
			</div>
			";
			$found = "1";
		}
		if ($found != "1") {
			print "<br><br>
			<div class=\"form-group\">
			<div class=\"col-sm-10\"><font color=blue>Sorry, there are no discounts.</font></div>
			</div>";
		}

	}

	public function payout() {
		print "<h2>Request Payout</h2>";

		if ($_GET['c'] == "") {

			print "<br>You are requesting either a payout on an event that has ended or a early payout on ticket sales. Early payout is available 14 days from the event end date.<br>";
			$sql = "SELECT `title` FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				print "<br><b>Event: $row[title]</b>";
			}

			print "<br>To confirm your request please click the button below.<br><br>";

			print "<input type=\"button\" class=\"btn btn-success\" value=\"Request Payout\" onclick=\"document.location.href='index.php?section=dashboard&center=payout&id=$_GET[id]&c=1'\">";
		}

		if ($_GET['c'] == "1") {
                        $sql = "SELECT `title` FROM `events` WHERE `id` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
				$title = $row['title'];
			}
			$subj = "Payout request for $title on Ticket Pointe";
			$msg = "Admin,<br>The event holder is requesting a payout for $title. Please log into your admin area to complete this request.<br>";
	                $settings = $this->get_settings();

                        $this->send_email($settings[2],$subj,$msg);

                        //mail($settings[2],$subj,$msg,$settings[3]);
			print "<br>Your request for a payout is processing.<br>";
			// RBS
		}

	}

	public function social() {

		$this->check_section('social');


                $sql = "SELECT * FROM `social` WHERE `userID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
			if ($row['facebook'] == "Yes") {
				$facebook = "<option selected value=\"Yes\">Yes (default)</option>";
			} else {
				$facebook = "<option selected value=\"No\">No (default)</option>";
			}

                        if ($row['google'] == "Yes") {
                                $google = "<option selected value=\"Yes\">Yes (default)</option>";
                        } else {
                                $google = "<option selected value=\"No\">No (default)</option>";
                        }

                        if ($row['linkedin'] == "Yes") {
                                $linkedin = "<option selected value=\"Yes\">Yes (default)</option>";
                        } else {
                                $linkedin = "<option selected value=\"No\">No (default)</option>";
                        }

                        if ($row['tumbler'] == "Yes") {
                                $tumbler = "<option selected value=\"Yes\">Yes (default)</option>";
                        } else {
                                $tumbler = "<option selected value=\"No\">No (default)</option>";
                        }

                        if ($row['twitter'] == "Yes") {
                                $twitter = "<option selected value=\"Yes\">Yes (default)</option>";
                        } else {
                                $twitter = "<option selected value=\"No\">No (default)</option>";
                        }


		}

		print "<form name=\"myform\">
		<div id=\"social_div\">
		";

                        print '
                                <div class="page-head">
                                        <h2 class="page-head-title">Social</h2>
                                </div>
                        ';


                        print "
                        <nav>
                                <ul class=\"pagination\">
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_details&id=$_GET[id]\">Details <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_design&id=$_GET[id]\">Design <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=edit_settings&id=$_GET[id]\">Settings <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=manage_tickets&id=$_GET[id]\">Tickets <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item\"><a href=\"index.php?section=dashboard&center=discounts&id=$_GET[id]\">Discounts <span class=\"sr-only\"></span></a></li>
                                <li class=\"page-item active\"><a href=\"index.php?section=dashboard&center=social\">Social <span class=\"sr-only\">(current)</span></a></li>
                                <li class=\"page-item\"><a href=\"javascript:void(0)\" onclick=\"window.open('download.php?id=$_GET[id]')\">Download Ticket List <span class=\"sr-only\"></span></a></li>

                                </ul>
                        </nav>
                        ";

		print "
		<div class=\"form-group\">
		<label class=\"col-sm-6 control-label\">Please select the social platform(s) that you would like visitors <br>to have the ability to share:</label>
		</div>

		<br><br><br>
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\"><img src=\"img/facebook.png\" alt=\"Facebook\" title=\"Facebook\"></label>
			<div class=\"col-sm-6\"><select name=\"facebook\">$facebook<option>Yes</option><option>No</option></select></div>
		</div>

		<br><br><br>
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\"><img src=\"img/google.png\" alt=\"Google\" title=\"Google\"></label>
                        <div class=\"col-sm-6\"><select name=\"google\">$google<option>Yes</option><option>No</option></select></div>
                </div>

		<br><br><br>
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\"><img src=\"img/linkedin.png\" alt=\"Linked In\" title=\"Linked In\"></label>
                        <div class=\"col-sm-6\"><select name=\"linkedin\">$linkedin<option>Yes</option><option>No</option></select></div>
                </div>

		<br><br><br>
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\"><img src=\"img/tumblr.png\" alt=\"Tumbler\" title=\"Tumbler\"></label>
                        <div class=\"col-sm-6\"><select name=\"tumbler\">$tumbler<option>Yes</option><option>No</option></select></div>
                </div>

		<br><br><br>
                <div class=\"form-group\">
                        <label class=\"col-sm-3 control-label\"><img src=\"img/twitter.png\" alt=\"Twitter\" title=\"Twitter\"></label>
                        <div class=\"col-sm-6\"><select name=\"twitter\">$twitter<option>Yes</option><option>No</option></select></div>
                </div>

		<br><br><br>
		<div class=\"form-group\">
			<label class=\"col-sm-3 control-label\"></label>
			<div class=\"col-sm-6\"><input type=\"button\" value=\"Save\" onclick=\"update_social(this.form)\" class=\"btn btn-primary\"></div>
		</div>


		</div></form>";

		?>
		<script>
		function update_social(myform) {
		        $.get('ajax/load.php?type=update_social',
		        $(myform).serialize(),
		        function(php_msg) {
		                $("#social_div").html(php_msg);
		        });
		}
		</script>

		<?php

	}

	public function update_social() {

		$sql = "SELECT * FROM `social` WHERE `userID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$found = "1";
		}

		if ($found == "1") {
			$sql = "UPDATE `social` SET `facebook` = '$_GET[facebook]', `google` = '$_GET[google]', `linkedin` = '$_GET[linkedin]', `tumbler` = '$_GET[tumbler]', `twitter` = '$_GET[twitter]' WHERE `userID` = '$_SESSION[id]'";
		} else {
			$sql = "INSERT INTO `social` (`facebook`,`google`,`linkedin`,`tumbler`,`twitter`,`userID`) VALUES ('$_GET[facebook]','$_GET[google]','$_GET[linkedin]','$_GET[tumbler]','$_GET[twitter]','$_SESSION[id]')";
		}
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>Your social settings was updated.</font><br>";
		} else {
			print "<br><font color=red>Your social settings failed to update.</font><br>";
		}
		$this->social();

	}

        public function OLDobjectToArray($d) {
	        if (is_object($d)) {
	        	// Gets the properties of the given object
		        // with get_object_vars function
        		$d = get_object_vars($d);
	        }

	        if (is_array($d)) {
	        	/*
		        * Return array converted to object
		        * Using __FUNCTION__ (Magic constant)
		        * for recursive call
		        */
		        return array_map(__FUNCTION__, $d);
		} else {
		        // Return array
		        return $d;
	        }
        }

	public function objectToArray( $object )
	   {
	       if( !is_object( $object ) && !is_array( $object ) )
	       {
        	   return $object;
	       }
	       if( is_object( $object ) )
	       {
	           $object = get_object_vars( $object );
	       }
	       //return array_map( 'objectToArray', $object ); // non PHP class way
		return array_map(array($this, 'objectToArray'), $object); // in a class you have to return array as the namespace


	   }

	public function save_view($eventID) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date("Ymd");
		$sql = "INSERT INTO `views` (`eventID`,`remote_ip`,`date`) VALUES ('$eventID','$ip','$date')";
		$result = $this->new_mysql($sql);
	}

	public function get_views($eventID) {
		$sql = "
		SELECT
			DISTINCT(`views`.`remote_ip`) AS 'IP',
			COUNT(`views`.`eventID`) AS 'total'

		FROM
			`views`

		WHERE
			`views`.`eventID` = '$eventID'
		";
		$total = "0";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$total = $row['total'];
		}
		return $total;

	}

	public function page_view() {
                $settings = $this->get_settings();

		$_SESSION['random'] = rand(10,400);
		$rand2 = ($_SESSION['random'] * 2) - 50;
		$settings = $this->get_settings();
		//print "T: ($_SESSION[random] * 2) -50<br>";

		if ($_GET['domain'] != "") {
			$sql = "SELECT * FROM `events` WHERE `homepage` = '$_GET[domain]'";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				$_GET['id'] = $row['id'];
			}
		}

		$this->save_view($_GET['id']);
		$page_views = $this->get_views($_GET['id']);
		$_SESSION['page_eventID'] = $_GET['id'];
		$sql = "
		SELECT
			`location`.`location`,
			`category`.`category`,
			`events`.`cover_image`,
			`events`.`id`,
			`events`.`address`,
			`events`.`title`,
			`events`.`tagline`,
			DATE_FORMAT(`events`.`start_date`, '%b %d, %Y') AS 'start_date',
			`events`.`start_date` AS 'date_start',
                        DATE_FORMAT(`events`.`end_date`, '%b %d, %Y') AS 'end_date',
			DATE_FORMAT(`events`.`end_date`, '%Y%m%d') AS 'end_date2',
			`events`.`end_date` AS 'date_end',
			`events`.`start_time`,
			`events`.`end_time`,
			`events`.`description`,
			`users`.`id` AS 'userID',
			`users`.`email`,
			`events`.`slide1`,
			`events`.`slide2`,
			`events`.`slide3`,
			`events`.`slide4`,
			`events`.`slide5`,
			`events`.`homepage`,
			`events`.`video`,
			`events`.`enable_donation`,
			`events`.`donation_goal`,
			`templates`.`filename`,
			`events`.`registration`

		FROM
			`events`,`location`,`category`,`users`,`templates`

		WHERE
			`events`.`id` = '$_GET[id]'
			AND `events`.`locationID` = `location`.`id`
			AND `events`.`categoryID` = `category`.`id`
			AND `events`.`userID` = `users`.`id`
			AND `events`.`templateID` = `templates`.`id`
		";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
            include "view_desktop_upbeat.php";
		}
	}



        public function page_view_mobile() {
                $settings = $this->get_settings();

                $_SESSION['random'] = rand(10,400);
                $rand2 = ($_SESSION['random'] * 2) - 50;
                $settings = $this->get_settings();
                //print "T: ($_SESSION[random] * 2) -50<br>";

                if ($_GET['domain'] != "") {
                        $sql = "SELECT * FROM `events` WHERE `homepage` = '$_GET[domain]'";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $_GET['id'] = $row['id'];
                        }
                }

                $this->save_view($_GET['id']);
                $page_views = $this->get_views($_GET['id']);
                $_SESSION['page_eventID'] = $_GET['id'];
                $sql = "
                SELECT
                        `location`.`location`,
                        `category`.`category`,
                        `events`.`cover_image`,
                        `events`.`id`,
                        `events`.`address`,
                        `events`.`title`,
                        `events`.`tagline`,
                        DATE_FORMAT(`events`.`start_date`, '%b %d, %Y') AS 'start_date',
                        `events`.`start_date` AS 'date_start',
                        DATE_FORMAT(`events`.`end_date`, '%b %d, %Y') AS 'end_date',
                        DATE_FORMAT(`events`.`end_date`, '%Y%m%d') AS 'end_date2',
                        `events`.`end_date` AS 'date_end',
                        `events`.`start_time`,
                        `events`.`end_time`,
                        `events`.`description`,
                        `users`.`id` AS 'userID',
			`users`.`email`,
                        `events`.`slide1`,
                        `events`.`slide2`,
                        `events`.`slide3`,
                        `events`.`slide4`,
                        `events`.`slide5`,
                        `events`.`homepage`

                FROM
                        `events`,`location`,`category`,`users`

                WHERE
                        `events`.`id` = '$_GET[id]'
                        AND `events`.`locationID` = `location`.`id`
                        AND `events`.`categoryID` = `category`.`id`
                        AND `events`.`userID` = `users`.`id`
                ";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                ?>
                <div id="page_view">
                <table border=0 width=320>
                        <tr>
                        <td width="320" valign=top>

			<h1><?=$row['title']?></h1>
			<h2><?=$row['tagline']?></h2>

                        <?=$row['start_date']?> to <?=$row['end_date']?><br><?=$row['start_time']?> to <?=$row['end_time']?><br><?=$row['location']?><br>
			<div id="timeleft"></div>

                        <img src="uploads/<?=$row['userID']?>/cover/<?=$row['id']?>/<?=$row['cover_image']?>" width="320">

			<br>
			<div id="word">
			<?=$row['description']?>
			</div>

			<br>
                        <iframe width="320" height="350" frameborder="0" style="border:0"
                                                src="https://www.google.com/maps/embed/v1/place?q=<?=$row['address']?>&key=AIzaSyD4rJhKUws_jnA1h8NttpfBsxWX4TwNWcY">
                                                </iframe>

			<br>
			<a href="mailto:<?=$row['email'];?>&subject=<?=$row['title'];?>"><span class="btn btn-primary">Contact Organizer</span></a>

			<br>

      <script type="text/javascript">
      function refreshDiv() {
         $('#timeleft').load('check_time.php?id=<?=$_GET['id']?>', function(){ /* callback code here */ });

      }
      setInterval(refreshDiv, 30000);
      </script>


                                                        <?php
                                                        $sql2 = "SELECT * FROM `tickets` WHERE `eventID` = '$row[id]'";
                                                        $result2 = $this->new_mysql($sql2);
                                                        for ($y=0; $y < 51; $y++) {
                                                                $qty .= "<option value=\"$y\">$y</option>";
                                                        }
                                                        $viewID = rand(50,500);
                                                        print "
                                                        <form name=\"myform\" action=\"index.php\" method=\"post\">
                                                        <input type=\"hidden\" name=\"id\" value=\"$row[id]\">
                                                        <input type=\"hidden\" name=\"section\" value=\"cart\">
                                                        <input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
                                                        <table>
                                                        <tr>
								<td width=100><b>Ticket:</b></td>
								<td width=100><b>Price:</b></td>
								<td width=100><b>Quantity:</b></td>
							</tr>";
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
                                                                $result3 = $this->new_mysql($sql3);
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
                                                                $check_today = date("Ymd");
                                                                if ($check_today > $row['end_date2']) {
                                                                        print "<tr><td colspan=3><font color=red>This event is now closed.</font></td></tr>";
                                                                } else {
                                                                        print "<tr><td colspan=3><input type=\"submit\" class=\"btn btn-primary\" value=\"Purchase Tickets\"></td></tr>";
                                                                }
                                                        }
                                                        print "</table>
                                                        </form>";

                                                        ?>

							<br>
                                                        <input type="button" class="btn btn-default" value="Add To Calendar"
                                                        onclick="window.open('ical.php?r=<?=$rand2?>&id=<?=$row['id'];?>')">

                                                        <?php
                                                        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                                                        ?>

                                                        <br><input type="button" class="btn btn-default" value="Share With Friends" onclick="window.location.href='mailto:?subject=Ticket Pointe&body=Hi please visit <?=$url;?>';">
							<br><?=$page_views;?> views<br><br>
                                                        <?php
                                                        $sql2 = "SELECT * FROM `social` WHERE `userID` = '$row[userID]'";
                                                        $result2 = $this->new_mysql($sql2);
                                                        while ($row2 = $result2->fetch_assoc()) {
                                                                if ($row['homepage'] != "") {
                                                                        $url = "http://" . $row['homepage'] . "." . $settings[8];
                                                                } else {
                                                                        $url = $settings[1] . "index.php?section=page_view&id=$_GET[id]"; // To Do
                                                                }
                                                                if ($row2['facebook'] == "Yes") {
                                                                        $this->social_link("facebook",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['google'] == "Yes") {
                                                                        $this->social_link("google",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['linkedin'] == "Yes") {
                                                                        $this->social_link("linkedin",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['tumbler'] == "Yes") {
                                                                        $this->social_link("tumbler",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['twitter'] == "Yes") {
                                                                        $this->social_link("twitter",$url,$row['title'],$row['tagline']);
                                                                }

                                                        }

                                                        ?>

<br>

<div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 300px; height: 300px; overflow: hidden; visibility: hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
        <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
        <div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
</div>
<div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 300px; height: 300px; overflow: hidden;">
        <?php
        for ($x=1; $x < 6; $x++) {
                $var = "slide";
                $var .= $x;
                if ($row[$var] != "") {
                        print "
                        <div data-p=\"112.50\" style=\"display: none;\">
                        <img data-u=\"image\" width=\"300px\" src=\"uploads/$row[userID]/slide/$row[id]/$row[$var]\" />
                        </div>
                        ";
                }
        }
        ?>

        <!-- Bullet Navigator -->
        <div data-u="navigator" class="jssorb01" style="bottom:16px;right:10px;">
                <div data-u="prototype" style="width:12px;height:12px;"></div>
        </div>
        <!-- Arrow Navigator -->
        <span data-u="arrowleft" class="jssora02l" style="top:123px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
        <span data-u="arrowright" class="jssora02r" style="top:123px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
</div>



		</td>
                </tr>
                </table>
                </div>
                <?php
                }
        }

	public function get_amounts($p) {
		switch ($p) {
			case "p1":
			$amount = "5.00";
			break;

			case "p2":
			$amount = "25.00";
			break;

			case "p3":
			$amount = "50.00";
			break;

			case "p4":
			$amount = "100.00";
			break;

			case "p5":
			$amount = "250.00";
			break;

			case "p6":
			$amount = $_POST['custom_amount'];
			if ($amount == "") {
				$amount = "5.00";
			}
			break;
		}
		if ($amount < 5) {
			$amount = "5.00";
		}
		return $amount;
	}

	public function donate() {
                print "<div id=\"page_view\">";


		$sql = "SELECT `title`,`more_info` FROM `events` WHERE `id` = '$_POST[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$more_info = $row['more_info'];
			$title = $row['title'];
		}

		$_SESSION['viewID'] = $_POST['viewID'];
		$_SESSION['amount'] = $this->get_amounts($_POST['donate']);
		print "<h2>Donate : $title</h2><br>
		Please complete the form below to process your donation. Please note the minumum amount to donate is $5.00. If you selected an amount under $5.00 your donation amount may have been corrected below.<br><br>
		Amount: $$_SESSION[amount]<br>";

		print "<br>
                <!--<input type=\"button\" name=\"more_info\" id=\"more_info\" class=\"btn btn-success\" value=\"More Info\" onclick=\"
                        document.getElementById('display_more_info').style.display='inline';
                        document.getElementById('more_info').style.display='none';
                \">-->
                </form>

                <div id=\"display_more_info\" style=\"display:none\"><br>$more_info<br>
		<br>
                <input type=\"button\" id=\"more_info_close\" class=\"btn btn-success\" value=\"Close\" onclick=\"
                        document.getElementById('display_more_info').style.display='none';
                        document.getElementById('more_info').style.display='inline';

                \">
		<br><br>
                </div>";

                $this->get_cc_form('donate.php');


		print "</div>";

	}

	public function cart() {

		print '
		<style>
		.top-buffer { margin-top:10px; margin-left:10px; margin-right:10px; }
		</style>
		';

                print "<div id=\"page_view2\"><div id=\"cart_inner\">";
		$sql = "
		SELECT
			DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
			DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
			`start_time`,
			`end_time`,
			`title`,
			`more_info`
		FROM
			`events`
		WHERE
			`events`.`id` = '$_POST[id]'

		";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$more_info = $row['more_info'];
			print "<div class\"row top-buffer\">
			<div class=\"col-sm-12\">";
			print "<h2>$row[title]</h2>
			<h3>$row[start_date] to $row[end_date] from $row[start_time] to $row[end_time]</h3>";
			print "</div></div>";
		}

		if ($_POST['act'] == "apply_code") {
			// look up discount
			$today = date("Y-m-d");
			$sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_POST[discount]' AND `expire` > '$today' LIMIT 1";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				$Dfound = "1";
				$amount_off = $row['amount_off'];
				$type = $row['amount_how'];
			}
			if ($Dfound != "1") {
				print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">";
				print "<font color=red>Sorry, the discount code entered was invalid or expired.</font><br>";
				print "</div></div>";
			}
		}

		print "
		<form action=\"index.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"checkout\">
		<input type=\"hidden\" name=\"id\" value=\"$_POST[id]\">
                <input type=\"hidden\" name=\"viewID\" value=\"$_POST[viewID]\">
		<div class=\"row top-buffer\">
			<div class=\"col-sm-2\"><b>Ticket</b></div>
			<div class=\"col-sm-2\"><b>Price</b></div>
			<div class=\"col-sm-3\"><b>Assign To Name</b></div>
			<div class=\"col-sm-3\"><b>Assign To Email</b></div>
			<div class=\"col-sm-2\"><b>Amount</b></div>
		</div>
		";

		$sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_POST[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$i = "qty";
			$i .= $row['id'];
			if ($_POST[$i] > 0) {
				// QTY check
		                $sql2 = "SELECT `qty`,`id` FROM `tickets` WHERE `id` = '$row[id]'";
		                $result2 = $this->new_mysql($sql2);
		                while ($row2 = $result2->fetch_assoc()) {
		                        $total_qty = $row2['qty'];
				}
	                        $sql2 = "
        	                SELECT
                	                SUM(`cart`.`qty`) AS 'total_sold'

                        	FROM
                                	`cart`
	                        WHERE
        	                        `cart`.`ticketID` = '$row[id]'
                	                AND `cart`.`status` = 'Paid'
                        	";
	                        $result2 = $this->new_mysql($sql2);
        	                while ($row2 = $result2->fetch_assoc()) {
                	                $total_sold = $row2['total_sold'];
	                        }
				$total_sold = $total_sold + $_POST[$i];
				if ($total_sold > $total_qty) {
					print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">";
					print "<font color=red>You have selected a quantity that is greater then what is available for <b>$row[name]</b>. Please click back and select a lesser quantity.</font>";
					print "</div></div>";
					$err = "1";
				} else {
					print "<input type=\"hidden\" name=\"$i\" value=\"$_POST[$i]\">";
					$amount = $row['price'] * $_POST[$i];
					for ($y=0; $y < $_POST[$i]; $y++) {
						$number++;
						print "<div class=\"row top-buffer\">
						<div class=\"col-sm-2\">$row[name]</div>
						<div class=\"col-sm-2\">$$row[price]</div>
						<div class=\"col-sm-3\"><input type=\"text\" name=\"name_$row[id]_$y\" id=\"name_$row[id]_$y\" required> <a href=\"javascript:void(0)\" onclick=\"copy_name()\" size=40>Copy To All Tickets</a></div>
							";
							$y2 = $y + 1;
							if ($y2 < $_POST[$i]) {
								$d_email .= "document.getElementById('email_$row[id]_$y2').value = document.getElementById('email_$row[id]_$y').value;\n";
                                                                $d_name .= "document.getElementById('name_$row[id]_$y2').value = document.getElementById('name_$row[id]_$y').value;\n";

							}

							print "
							<div class=\"col-sm-3\"><input type=\"text\" name=\"email_$row[id]_$y\" id=\"email_$row[id]_$y\" required> <a href=\"javascript:void(0)\" onclick=\"copy_email()\" size=60>Copy To All Tickets</a></div>
							<div class=\"col-sm-2\">$".number_format($row['price'],2,'.',',')."</div>
						</div>";

						if ($row['more_info'] != "") {
							print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">$row[more_info]</div></div>";
						}

					}
					$total = $total + $amount;
				}
			}
		}

		?>
		<script>
		function copy_name() {
			<?php echo $d_name;?>
		}
                function copy_email() {
                        <?php echo $d_email;?>
                }

		</script>
		<?php


		if ($total > 0) {

			if ($Dfound == "1") {
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
			if ($err != "1") {
		                $fees = $this->get_fees($total,$number);
        	                print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Service Fee's</td><td>$".number_format($fees,2,'.',',')."</div></div>";
                	        $grand_total = $total + $fees;

				if ($total_discount != "") {
					print "<div class=\"row top-buffer\"><div class=\"col-sm-6\">Discount:</div><div class=\"col-sm-6\">$".number_format($total_discount,2,'.',',')."</div>";
				}

	                        print "<div class=\"row top-buffer\"><div class=\"col-sm-6\">Total:</div><div class=\"col-sm-6\">$".number_format($grand_total,2,'.',',')."</div></div>";
				print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">To make any changes click back or if you are ready click check out.</div></div>
				<div class=\"row top-buffer\"><div class=\"col-sm-12\"><div id=\"timeleft\"></div><br>
				<input type=\"checkbox\" name=\"terms\" value=\"checked\" required>&nbsp;

				I acknowledge that I have read and agree with Ticket Pointe's <a href=\"terms.html\">terms of service</a>.&nbsp;&nbsp;

				<input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\">
				";
			}

			?>
		      <script type="text/javascript">
		      function refreshDiv() {
		         $('#timeleft').load('check_time2.php', function(){ /* callback code here */ });

		      }
		      setInterval(refreshDiv, 1000);
		      </script>
			<?php

			print "
			</div></div>";

		} else {
			if ($number > 0) {
                                print "<div class=\"row top-buffer\"><div class=\"col-sm-12\"><input type=\"checkbox\" name=\"terms\" value=\"checked\" required>&nbsp;
                                 I acknowledge that I have read and agree with Ticket Pointe's <a href=\"terms.html\">terms of service</a>.
			&nbsp;&nbsp;<input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\"></div></div>";
			} else {
				print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Sorry, you did not add any tickets to your cart.</div></div>";
			}
		}
		print "<input type=\"hidden\" name=\"discount\" value=\"$_POST[discount]\">";
		print "</form>";


		if ($Dfound != "1") {
		print "<form action=\"index.php\" method=\"post\">
		";
		foreach ($_POST as $key=>$value) {
			print "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
		}
		print "
		<div class=\"row top-buffer\"><div class=\"col-sm-12\"><input type=\"hidden\" name=\"act\" value=\"apply_code\">
		Discount Code: <input type=\"text\" name=\"discount\" size=20> <input type=\"submit\" class=\"btn btn-primary\" value=\"Apply Discount\"> &nbsp;&nbsp;
		<!--<input type=\"button\" name=\"more_info\" id=\"more_info\" class=\"btn btn-success\" value=\"More Info\" onclick=\"
			document.getElementById('display_more_info').style.display='inline';
			document.getElementById('more_info').style.display='none';
		\">-->
		</form>

		<div id=\"display_more_info\" style=\"display:none\"><br>$more_info<br>
		<input type=\"button\" id=\"more_info_close\" class=\"btn btn-success\" value=\"Close\" onclick=\"
                        document.getElementById('display_more_info').style.display='none';
                        document.getElementById('more_info').style.display='inline';

		\">
		</div>
		</div></div>

		";
		}
		print "</div></div>";
	}

        public function cart_iframe() {
		$device = $this->device_type();

                $sql = "
                SELECT
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
                        `start_time`,
                        `end_time`,
                        `title`,
			`more_info`
                FROM
                        `events`
                WHERE
                        `events`.`id` = '$_POST[id]'

                ";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $more_info = $row['more_info'];
                        print "<h2>$row[title]</h2>
                        <h3>$row[start_date] to $row[end_date] from $row[start_time] to $row[end_time]</h3>";
                }

                if ($_POST['act'] == "apply_code") {
                        // look up discount
                        $today = date("Y-m-d");
                        $sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_POST[discount]' AND `expire` > '$today' LIMIT 1";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row['amount_off'];
                                $type = $row['amount_how'];
                        }
                        if ($Dfound != "1") {
                                print "<font color=red>Sorry, the discount code entered was invalid or expired.</font><br>";
                        }
                }

                print "
                <form action=\"tickets_iframe.php\" method=\"post\">
                <input type=\"hidden\" name=\"section\" value=\"checkout\">
                <input type=\"hidden\" name=\"id\" value=\"$_POST[id]\">
                <input type=\"hidden\" name=\"viewID\" value=\"$_POST[viewID]\">
		";

		if ($device == "0") {
			print "
        	        <table class=\"table\">
                	<tr>
                        	<td><b>Ticket</b></td>
	                        <td><b>Price</b></td>
        	                <td><b>Assign To Name</b></td>
				<td><b>Assign To Email</b></td>
                	        <td><b>Amount</b></td>
	                </tr>";
		} else {
                        print "
                        <table class=\"table\">
			";
		}

                $sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i = "qty";
                        $i .= $row['id'];
                        if ($_POST[$i] > 0) {
                                // QTY check
                                $sql2 = "SELECT `qty`,`id` FROM `tickets` WHERE `id` = '$row[id]'";
                                $result2 = $this->new_mysql($sql2);
                                while ($row2 = $result2->fetch_assoc()) {
                                        $total_qty = $row2['qty'];
                                }
                                $sql2 = "
                                SELECT
                                        SUM(`cart`.`qty`) AS 'total_sold'

                                FROM
                                        `cart`
                                WHERE
                                        `cart`.`ticketID` = '$row[id]'
                                        AND `cart`.`status` = 'Paid'
                                ";
                                $result2 = $this->new_mysql($sql2);
                                while ($row2 = $result2->fetch_assoc()) {
                                        $total_sold = $row2['total_sold'];
                                }
                                $total_sold = $total_sold + $_POST[$i];
                                if ($total_sold > $total_qty) {
					if ($device == "0") {
	                                        print "<tr><td colspan=4><font color=red>You have selected a quantity that is greater then what is available for <b>$row[name]</b>. Please click back and select a lesser quantity.</font></td></tr>";
					} else {
						print "<tr><td><font color=red>You have selected a quantity that is greater then what is available for <b>$row[name]</b>. Please click back and select a lesser quantity.</font></td></tr>";
					}
                                        $err = "1";
                                } else {
                                        print "<input type=\"hidden\" name=\"$i\" value=\"$_POST[$i]\">";
                                        $amount = $row['price'] * $_POST[$i];
					//$amount = $row['price'];

                                        for ($y=0; $y < $_POST[$i]; $y++) {
							$number++;
							if ($device == "0") {
							// begin
                                        	        print "<tr>
                                                        <td>$row[name]</td>
                                                        <td>$$row[price]</td>
                                                        <td><input type=\"text\" name=\"name_$row[id]_$y\" id=\"name_$row[id]_$y\" required> <a href=\"javascript:void(0)\" onclick=\"copy_name()\" size=40>Copy To All Tickets</a></td>
                                                        ";
                                                        $y2 = $y + 1;
                                                        if ($y2 < $_POST[$i]) {
                                                                $d_email .= "document.getElementById('email_$row[id]_$y2').value = document.getElementById('email_$row[id]_$y').value;\n";
                                                                $d_name .= "document.getElementById('name_$row[id]_$y2').value = document.getElementById('name_$row[id]_$y').value;\n";

                                                        }

                                                        print "
                                                        <td><input type=\"text\" name=\"email_$row[id]_$y\" id=\"email_$row[id]_$y\" required> <a href=\"javascript:void(0)\" onclick=\"copy_email()\" size=60>Copy To All Tickets</a></td></td>
                                                        <td>$".number_format($row['price'],2,'.',',')."</td>
	                                                </tr>";

        	                                        if ($row['more_info'] != "") {
                	                                        //print "<tr><td colspan=5>$row[more_info]</td></tr>";
                        	                        }
							// end
							} else {
                                                        // begin
                                                        print "
							<tr><td>$row[name]</td></tr>
                                                        <tr><td>Price: $$row[price]</td></tr>
                                                        <tr><td>Assign To Name: <input type=\"text\" name=\"name_$row[id]_$y\" id=\"name_$row[id]_$y\" required size=\"10\"><br>
								<a href=\"javascript:void(0)\" onclick=\"copy_name()\" size=40>Copy To All Tickets</a></td></tr>
                                                        ";
                                                        $y2 = $y + 1;
                                                        if ($y2 < $_POST[$i]) {
                                                                $d_email .= "document.getElementById('email_$row[id]_$y2').value = document.getElementById('email_$row[id]_$y').value;\n";
                                                                $d_name .= "document.getElementById('name_$row[id]_$y2').value = document.getElementById('name_$row[id]_$y').value;\n";

                                                        }
                                                        print "
                                                        <tr><td>Assign To Email: <input type=\"text\" name=\"email_$row[id]_$y\" id=\"email_$row[id]_$y\" required size=\"10\"><br>
							 <a href=\"javascript:void(0)\" onclick=\"copy_email()\" size=60>Copy To All Tickets</a></td></td></tr>
                                                        <tr><td>Amount: $".number_format($row['price'],2,'.',',')."</td></tr>
                                                        ";

                                                        if ($row['more_info'] != "") {
                                                               // print "<tr><td colspan=5>$row[more_info]</td></tr>";
                                                        }
                                                        // end
							}

                                        }
                                        $total = $total + $amount;
                                }

                ?>
                <script>
                function copy_name() {
                        <?php echo $d_name;?>
                }
                function copy_email() {
                        <?php echo $d_email;?>
                }

                </script>
                <?php


                        }
                }
                if ($total > 0) {

                        if ($Dfound == "1") {
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
			if ($err != "1") {
	                        $fees = $this->get_fees($total,$number);
				if ($device == "0") {
        	                print "<tr><td colspan=3>Service Fee's</td><td>$".number_format($fees,2,'.',',')."</td></tr>";
				} else {
                                print "<tr><td>Service Fee: $".number_format($fees,2,'.',',')."</td></tr>";
				}
                	        $grand_total = $total + $fees;

	                        if ($total_discount != "") {
					if ($device == "0") {
        	                        print "<tr><td colspan=3>Discount:</td><td>$".number_format($total_discount,2,'.',',')."</td></tr>";
					} else {
                                        print "<tr><td>Discount: $".number_format($total_discount,2,'.',',')."</td></tr>";
					}
                	        }
				if ($device == "0") {
	                        print "<tr><td colspan=3>Total:</td><td>$".number_format($grand_total,2,'.',',')."</td></tr>";
        	                print "<tr><td colspan=4>To make any changes click back or if you are ready click check out.</td></tr>
                	        <tr><td colspan=3><div id=\"timeleft\"></div></td><td><input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\"></td></tr>
	                        ";
				} else {
                                print "<tr><td>Total: $".number_format($grand_total,2,'.',',')."</td></tr>";
                                print "<tr><td>To make any changes click back or if you are ready click check out.</td></tr>
                                <tr><td><div id=\"timeleft\"></div><input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\"></td></tr>";
				}
			}
                        ?>
                      <script type="text/javascript">
                      function refreshDiv() {
                         $('#timeleft').load('check_time2.php', function(){ /* callback code here */ });

                      }
                      setInterval(refreshDiv, 1000);
                      </script>
                        <?php

                } else {
                        if ($number > 0) {
				if ($device == "0") {
                                print "<tr><td colspan=3>&nbsp;</td><td><input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\"></td></tr>";
				} else {
                                print "<tr><td><input type=\"submit\" class=\"btn btn-primary\" id=\"ck\" value=\"Check Out\"></td></tr>";
				}
                        } else {
				if ($device == "0") {
                                print "<tr><td colspan=4>Sorry, you did not add any tickets to your cart.</td></tr>";
				} else {
                                print "<tr><td>Sorry, you did not add any tickets to your cart.</td></tr>";
				}
                        }
                }
                print "</table>";
                print "<input type=\"hidden\" name=\"discount\" value=\"$_POST[discount]\">";
                print "</form>";


                if ($Dfound != "1") {
                print "<form action=\"tickets_iframe.php\" method=\"post\">
                ";
                foreach ($_POST as $key=>$value) {
                        print "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
                }
                print "
                <input type=\"hidden\" name=\"act\" value=\"apply_code\">
                Discount Code: <input type=\"text\" name=\"discount\" size=20> <input type=\"submit\" class=\"btn btn-primary\" value=\"Apply Discount\">

		&nbsp;&nbsp;
                </form>

                <div id=\"display_more_info\" style=\"display:none\"><br>$more_info<br>
                <input type=\"button\" id=\"more_info_close\" class=\"btn btn-success\" value=\"Close\" onclick=\"
                        document.getElementById('display_more_info').style.display='none';
                        document.getElementById('more_info').style.display='inline';

                \">
                </div>



                </form>
                ";
                }
        }

	public function view_html_page() {
                print "<div id=\"page_view\">";
		$sql = "SELECT * FROM `pages` WHERE `id` = '$_GET[id]'";
		$result = $this->new_mysql($sql);
		$row = $result->fetch_assoc();
		print "$row[content]";

		print "</div>";

	}

	public function cancel() {
		session_destroy();
		$_SESSION['time1'] = "";
		$_SESSION['time2'] = "";
                print "<div id=\"page_view\">";

		print "<h2>Inactive Order</h2><br>
		<font color=red>Your time expired on your order. If you would like to purchase tickets please visit the event and purchase new tickets.</font><br><br>";
		print "</div>";
	}


	public function cart_checkout() {
                print '
                <style>
                .top-buffer { margin-top:10px; margin-left:10px; margin-right:10px; }
                </style>
                ';

		$sesID = session_id();

                $sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i = "qty";
                        $i .= $row['id'];
			$qty = $_POST[$i];
			if ($qty > 0) {
				$date = date("Ymd");
				$time = date("H:i");
				$sql2 = "SELECT * FROM `cart` WHERE `sessionID` = '$sesID' AND `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `ticketID` = '$row[id]'";
				$result2 = $this->new_mysql($sql2);
				$found = "0";
				while ($row2 = $result2->fetch_assoc()) {
					$found = "1";
				}
				if ($found == "0") {
					$sql2 = "INSERT INTO `cart` (`sessionID`,`viewID`,`eventID`,`ticketID`,`description`,`price`,`qty`,`status`,`date`,`time`) VALUES
					('$sesID','$_POST[viewID]','$_POST[id]','$row[id]','$row[name]','$row[price]','$qty','Pending','$date','$time')";
					$result2 = $this->new_mysql($sql2);
				}
			}

		}
                print "<div id=\"page_view\">";
		print "<div id=\"timeleft\"></div>";
		?>
                      <script type="text/javascript">
                      function refreshDiv() {
                         $('#timeleft').load('check_time2.php', function(){ /* callback code here */ });

                      }
                      setInterval(refreshDiv, 1000);
                      </script>

		<?
                $sql = "
                SELECT
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
                        `start_time`,
                        `end_time`,
                        `title`
                FROM
                        `events`
                WHERE
                        `events`.`id` = '$_POST[id]'
                ";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        print "
			<div class=\"row top-buffer\"><div class=\"col-sm-12\">
				<h2>$row[title]</h2>
                        	<h3>$row[start_date] to $row[end_date] from $row[start_time] to $row[end_time]</h3></div></div>";
                }
		$sql = "SELECT `price`,`qty` FROM `cart` WHERE `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `sessionID` = '$sesID'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$price = $row['price'] * $row['qty'];
			$total = $total + $price;
			$number = 1 * $row['qty'];
		}
                if ($_POST['discount'] != "") {
                        $today = date("Y-m-d");
                        $sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_POST[discount]' AND `expire` > '$today' LIMIT 1";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row['amount_off'];
                                $type = $row['amount_how'];
                        }
                }
                        if ($Dfound == "1") {
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
		$total2 = $total;
                $fees = $this->get_fees($total,$number);
		$total = $total + $fees;
		if ($Dfound == "1") {
			print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Discount: $".number_format($total_discount,2,'.',',')."</div></div>";
			$_SESSION['discount'] = $_POST['discount'];
		}



		if ($total2 > 0) {
			print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Total: $".number_format($total,2,'.',',')."<br>Please complete the credit card form below. Be sure to enter in a valid email so you can receive your e-tickets.</div></div>";
			$this->get_cc_form('index.php');
		}

		if ($total2 == "0") {
			$this->get_form_free('index.php');
		}
		print "</div>";	
	}


        public function cart_checkout_iframe() {
                print '
                <style>
                .top-buffer { margin-top:10px; margin-left:10px; margin-right:10px; }
                </style>
                ';

                $sesID = session_id();

                $sql = "SELECT * FROM `tickets` WHERE `eventID` = '$_POST[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i = "qty";
                        $i .= $row['id'];
                        $qty = $_POST[$i];
                        if ($qty > 0) {
                                $date = date("Ymd");
                                $time = date("H:i");
                                $sql2 = "SELECT * FROM `cart` WHERE `sessionID` = '$sesID' AND `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `ticketID` = '$row[id]'";
                                $result2 = $this->new_mysql($sql2);
                                $found = "0";
                                while ($row2 = $result2->fetch_assoc()) {
                                        $found = "1";
                                }
                                if ($found == "0") {
                                        $sql2 = "INSERT INTO `cart` (`sessionID`,`viewID`,`eventID`,`ticketID`,`description`,`price`,`qty`,`status`,`date`,`time`) VALUES
                                        ('$sesID','$_POST[viewID]','$_POST[id]','$row[id]','$row[name]','$row[price]','$qty','Pending','$date','$time')";
                                        $result2 = $this->new_mysql($sql2);
                                }
                        }

                }

                print "<div id=\"timeleft\"></div>";
                ?>
                      <script type="text/javascript">
                      function refreshDiv() {
                         $('#timeleft').load('check_time2.php', function(){ /* callback code here */ });

                      }
                      setInterval(refreshDiv, 1000);
                      </script>

                <?
                $sql = "
                SELECT
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
                        `start_time`,
                        `end_time`,
                        `title`
                FROM
                        `events`
                WHERE
                        `events`.`id` = '$_POST[id]'
                ";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        print "<div class=\"row top-buffer\"><div class=\"col-sm-12\"><h2>$row[title]</h2>
                        <h3>$row[start_date] to $row[end_date] from $row[start_time] to $row[end_time]</h3></div></div>";
                }
                $sql = "SELECT `price`,`qty` FROM `cart` WHERE `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `sessionID` = '$sesID'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $price = $row['price'] * $row['qty'];
                        $total = $total + $price;
			$number = 1 * $row['qty'];
                }
                if ($_POST['discount'] != "") {
                        $today = date("Y-m-d");
                        $sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_POST[discount]' AND `expire` > '$today' LIMIT 1";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row['amount_off'];
                                $type = $row['amount_how'];
                        }
                }
                        if ($Dfound == "1") {
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
                $total2 = $total;

                $fees = $this->get_fees($total,$number);
                $total = $total + $fees;
                if ($Dfound == "1") {
                        print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Discount: $".number_format($total_discount,2,'.',',')."</div></div>";
                        $_SESSION['discount'] = $_POST['discount'];
                }

                if ($total2 > 0) {
                        print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">Total: $".number_format($total,2,'.',',')."<br>Please complete the credit card form below. Be sure to enter in a valid email so you can receive your e-tickets.</div></div>";
                        $this->get_cc_form('tickets_iframe.php');
                }

                if ($total2 == "0") {
                        $this->get_form_free('tickets_iframe.php');
                }

        }



	public function get_states() {
		$sql = "SELECT * FROM `state` ORDER BY `state` ASC";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$options .= "<option value=\"$row[state_abbr]\">$row[state]</option>";
		}
		return $options;
	}

	public function get_fees($charge,$tickets) {
		$processor = "0.015";
		$service_fee = "1.00";
		$transaction = "0.02";
		$t1 = $charge * $processor;
		$t2 = $service_fee * $tickets;
		$t3 = ($charge + $t1 + $t2) * $transaction;
		$t4 = $t1 + $t2 + $t3;

		return $t4;

	}

	public function donate_payment($total) {
		$settings = $this->get_settings();
                $sesID = session_id();
                include "class/gwapi.class.php";
                $gw = new gwapi;
		$gw->setLogin($settings[10], $settings[11]);
                $name = explode(" ",$_POST['name']);
                $gw->setBilling($name[0],$name[1],"",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['phone'],$_POST['phone'],$_POST['email'],"www.ticketpointe.com");
                $gw->setShipping($name[0],$name[1],"na",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['email']);
                $ordernumber = rand(50,1000);
                $ip = $_SERVER['REMOTE_ADDR'];
                $gw->setOrder($ordernumber,"TicketPointe",1, 2, $ordernumber,$ip);

                $r = $gw->doSale($total,$_POST['ccNo'],"$_POST[expMonth]$_POST[expYear]",$_POST['cvv']); // amount, CC, EXP MMYY, CVV
                $url = $gw->responses['responsetext'];
                $data =explode("=",$url);
                if ($data[0] == "Approved") { // was set to SUCCESS
                        echo "Thanks for your Donation!";
                                $date = date("Ymd");
                                $time = date("H:i");

			// do sql
			$sql = "INSERT INTO `donate` (`sessionID`,`viewID`,`eventID`,`price`,`status`,`date`,`time`,`email`,`name`,`addr1`,`city`,`state`,`zip`)
			VALUES ('$sesID','$_POST[viewID]','$_POST[id]','$total','Paid','$date','$time','$_POST[email]','$_POST[name]','$_POST[addr1]','$_POST[city]','$_POST[state]','$_POST[zip]')";
			$result = $this->new_mysql($sql);
			$this->payment_notification('donate',$_POST['id']);

                } else {
                        print "<br><font color=red>There was an error processing your order. Please try another credit card or call your bank then try again.</font><br><br>";
                        print "Error: $data[0]\n";
                }

	}

	public function payment_notification($type,$eventID) {
                $settings = $this->get_settings();
		switch ($type) {
			case "cart":
				$payment_type = "Ticket";
	
			break;

			case "donate":
				$payment_type = "Donation";
			break;
		}

		$sql = "
		SELECT 
			`events`.`title`,
			`users`.`email`,
			`users`.`fname`,
			`users`.`lname` 

		FROM 
			`events`,`users`

		WHERE 
			`events`.`id` = '$eventID'
			AND `events`.`userID` = `users`.`id`
			AND `events`.`notifications` = 'Yes'
		";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$subj = "New $payment_type on Ticket Pointe";
			$msg = "$row[fname] $row[lname],<br><br>You have received a new $payment_type payment for $row[title]";
                        $this->send_email($row['email'],$subj,$msg);
			//mail($row['email'],$subj,$msg,$settings[3]);
		}
	}

	public function registration_notification($eventID) {
                $settings = $this->get_settings();

		$sql = "
		SELECT 
			`events`.`title`,
                        `users`.`email`,
                        `users`.`fname`,
                        `users`.`lname` 


		FROM `events`, `users`

		WHERE 
			`events`.`id` = '$eventID'
			AND `events`.`userID` = `users`.`id`
                        AND `events`.`notifications` = 'Yes'

		";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $subj = "New registration on Ticket Pointe";
                        $msg = "$row[fname] $row[lname],<br><br>You have received a new registration for $row[title]";
                        $this->send_email($row['email'],$subj,$msg);
                        //mail($row['email'],$subj,$msg,$settings[3]);
		}

	}

	public function new_order_process() {
                $settings = $this->get_settings();
                $sesID = session_id();

			// split the current order into seperate orders
			$ok = "0";
			$sql = "SELECT * FROM `cart` WHERE `sessionID` = '$sesID' AND `viewID` = '$_POST[viewID]'";
			$result = $this->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				$i = "qty";
				$i .= $row['ticketID'];
				$qty = $_POST[$i];
				$qty2 = $qty2 + $qty;


				for ($y=0; $y < $qty; $y++) {
					$i2 = "name_";
					$i2 .= $row['ticketID'];
					$i2 .= "_$y";
					$name = $_POST[$i2];

                                        $i2 = "email_";
                                        $i2 .= $row['ticketID'];
                                        $i2 .= "_$y";
                                        $email = $_POST[$i2];

					$orderID = $row['viewID'] . $y;
					$date = date("Ymd");
					$time = date("H:i");
					$sql2 = "INSERT INTO `cart` (`sessionID`,`viewID`,`eventID`,`ticketID`,`description`,`price`,`qty`,`status`,`date`,`time`,`email`,`consumed`,`name`) VALUES
					('$sesID','$orderID','$row[eventID]','$row[ticketID]','$row[description]','$row[price]','1','Paid','$date','$time','$email','No','$name')";
					$result2 = $this->new_mysql($sql2);
					if ($result2 == "TRUE") {
						// email
						// disabled for test
		                                $image = $this->qr_code($sesID,$orderID);
						$passbook = $this->qr_code_passbook($sesID,$orderID);
                	                        $subj = "Your tickets from Ticket Pointe";
                        	                $msg = "$name,<br><br>Thank you for ordering your tickets from Ticket Pointe. Please check your email for your tickets. If the email is not delivered please check your spam folder.<br>$html";


	                                        // get event details
        	                                $sql10 = "
                	                        SELECT 
                        	                *,
                                	        DATE_FORMAT(`start_date`, '%m/%d/%Y') AS 'e_start',
                                        	DATE_FORMAT(`end_date`, '%m/%d/%Y') AS 'e_end'

	                                         FROM `events` WHERE `id` = '$row[eventID]'";
        	                                $result10 = $this->new_mysql($sql10);
                	                        while ($row10 = $result10->fetch_assoc()) {
                        	                        $e_title = $row10['title'];
                                	                $e_location = $row10['address'];
                                        	        $e_start = $row10['e_start'];
                                                	$e_end = $row10['e_end'];
	                                                $e_time1 = $row10['start_time'];
        	                                        $e_time2 = $row10['end_time'];
                	                        }

                        	                // email template
						// disabled for test
                                	        $msg = $this->email_template($name,$image,$e_title,$e_location,$e_start,$e_end,$e_time1,$e_time2,$passbook);
	

						// disabled for test
                	                        $this->send_email($email,$subj,$msg);



						// end email

						$ok++;
			                        if ($Dfound == "1") {
			                                $sql3 = "INSERT INTO `cart_discount` (`viewID`,`eventID`,`amount_off`,`type`) VALUES ('$_POST[viewID]','$_POST[id]','$amount_off','$type')";
			                                $result3 = $this->new_mysql($sql3);
			                        }
					}
					// disabled for test
		                        $this->payment_notification('cart',$_POST['id']);
				}

			}
		echo "Thanks for your Order! Please check your email for your tickets.";
		$url = "index.php?section=page_view&id=$_POST[id]";
		print "<br>Loading please wait... <a href=\"$url\">Click here if the page does not load.</a><br>";
		print "<meta http-equiv=\"refresh\" content=\"6;url=$url\">";


	}

	public function email_template($name,$image,$e_title,$e_location,$e_start,$e_end,$e_time1,$e_time2,$passbook='') {

		//include_once "email_template.php";

$template = '

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
        <title></title> 

        
    <style type="text/css">

        html,
        body {
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        body {
                margin: 0 !important;
        }
        
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        
        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }
        
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
                
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            Margin: 0 auto !important;
        }
        table table table {
            table-layout: auto; 
        }
        
        img {
            -ms-interpolation-mode:bicubic;
        }
        
        .yshortcuts a {
            border-bottom: none !important;
        }
        
        .mobile-link--footer a,
        a[x-apple-data-detectors] {
            color:inherit !important;
            text-decoration: underline !important;
        }
      
    </style>
    
    <style>
        
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
            }

            .fluid,
            .fluid-centered {
                max-width: 100% !important;
                height: auto !important;
                Margin-left: auto !important;
                Margin-right: auto !important;
            }
            .fluid-centered {
                Margin-left: auto !important;
                Margin-right: auto !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            .stack-column-center {
                text-align: center !important;
            }
        
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                Margin-left: auto !important;
                Margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }
                
        }

    </style>

</head>
<body bgcolor="#808080" width="100%" style="Margin: 0;">
<table bgcolor="#808080" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" style="border-collapse:collapse;"><tr><td valign="top">
    <center style="width: 100%;">

        <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
           Now you can add your ticket to Apple Pass Book 
        </div>

        <table align="center" width="600" class="email-container">
                        <tr>
                                <td style="padding: 20px 0; text-align: center">
                                        <img src="http://ticketpointe.com/img/logom.png" width="300" height="80" alt="logo" border="0">
                                </td>
                        </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="600" class="email-container">
                       
           
            <tr>
                <td dir="rtl" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            
                            <td width="66.66%" class="stack-column-center">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 10px; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;">
                                            
                                            '.$name.'

                                            ,</strong>
                                            <br><br>
                                            Thank you for ordering your tickets from Ticket Pointe. Please use this email as admission to your event.
                                            <br><br>
                                            <br><br><b>Event: 

                                            '.$e_title.'</b><br>
                                            Location: '.$e_location.'<br>
                                            Valid from '.$e_start.' to '.$e_end.'<br>
                                            Operating Hours: '.$e_time1.' to '.$e_time2.'<br><br>  
						To add to your Apple Passbook please click <a href="https://ticketpointe.com/pass/PHP-PKPass/event.php?q='.$passbook.'&e='.$e_title.'&l='.$e_location.'&d1='.$e_start.'&d2='.$e_end.'&t1='.$e_time1.'&t2='.$e_time2.'">here</a>


                                        </td>
                                    </tr>
                                </table>
                            </td>
                            
                            <td width="33.33%" class="stack-column-center">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                        
                                            


                                            <img src="'.$image.'" width="170" width="170" alt="ticket" border="0" class="center-on-narrow">



                                        <br><br>Ticket Type: RSVP<br>Quantity: 

                                        1


                                        <br><br><br>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
          
        <table align="center" width="600" class="email-container">
            <tr>
                <td style="padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #ffffff;">
                    <br><br>
                    Ticket Pointe, LLC<br><span class="mobile-link--footer">P.O. Box 12657, Durham, NC, 27709</span><br>
                    <br><br> 
                </td>
            </tr>
        </table>

    </center>
</td></tr></table>
</body>
</html>

';

		return $template;
	}


	public function send_email($email,$subj,$msg) {

		require_once('PHPMailer/PHPMailerAutoload.php');

		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = "mx.customphpdesign.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 587;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = "tickets@ticketpointe.customphpdesign.com";
		//Password to use for SMTP authentication
		$mail->Password = "1teamwork!";
		//Set who the message is to be sent from
		$mail->setFrom('tickets@ticketpointe.customphpdesign.com', 'Ticket Pointe');
		//Set an alternative reply-to address
		$mail->addReplyTo('tickets@ticketpointe.customphpdesign.com', 'Ticket Pointe');
		//Set who the message is to be sent to
		$mail->addAddress($email);
		//Set the subject line
		$mail->Subject = $subj;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($msg);
		//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if (!$mail->send()) {
		    //echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    //echo "Message sent!";
		}


	}


	public function free() {
                $settings = $this->get_settings();
                $sesID = session_id();
                print "<div id=\"page_view\">";
		if ($_POST['token'] != $_SESSION['token']) {
			print "<br><font color=red>Error: Your session has timed out.</font><br>";
			print "</div>";
			die;
		}
		$this->new_order_process();
		print "</div>";
	}

        public function free_iframe() {
                $settings = $this->get_settings();
                $sesID = session_id();
                if ($_POST['token'] != $_SESSION['token']) {
                        print "<br><font color=red>Error: Your session has timed out.</font><br>";
                        print "</div>";
                        die;
                }
                $this->new_order_process();
        
        }

	public function payment() {
                $sesID = session_id();
                print "<div id=\"page_view\">";
                $sql = "SELECT `price`,`qty` FROM `cart` WHERE `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `sessionID` = '$sesID'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $price = $row['price'] * $row['qty'];
                        $total = $total + $price;
                        $number++;
                }
                if ($_SESSION['discount'] != "") {
                        $today = date("Y-m-d");
                        $sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_SESSION[discount]' AND `expire` > '$today' LIMIT 1";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row['amount_off'];
                                $type = $row['amount_how'];
                        }
                }
                        if ($Dfound == "1") {
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
                $fees = $this->get_fees($total,$number);
                $total = $total + $fees;
                $total = number_format($total,2,'.',',');

                $settings = $this->get_settings();
		include "class/gwapi.class.php";
		$gw = new gwapi;
		$gw->setLogin($settings[10], $settings[11]);
		$name = explode(" ",$_POST['name']);
		$gw->setBilling($name[0],$name[1],"",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['phone'],$_POST['phone'],$_POST['email'],"www.ticketpointe.com");
		$gw->setShipping($name[0],$name[1],"na",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['email']);
		$ordernumber = rand(50,1000);
		$ip = $_SERVER['REMOTE_ADDR'];
		$gw->setOrder($ordernumber,"TicketPointe",1, 2, $ordernumber,$ip);
		$r = $gw->doSale($total,$_POST['ccNo'],"$_POST[expMonth]$_POST[expYear]",$_POST['cvv']); // amount, CC, EXP MMYY, CVV
		$url = $gw->responses['responsetext'];
		$data =explode("=",$url);
		if ($data[0] == "Approved") { // SUCCESS
  			$this->new_order_process();
		} else {
	                print "<br><font color=red>There was an error processing your order. Please try another credit card or call your bank then try again.</font><br><br>";
		        print "Error: $data[0]\n";
		}
		print "</div>";
	}


        public function payment_iframe() {
                $sesID = session_id();
                $sql = "SELECT `price`,`qty` FROM `cart` WHERE `viewID` = '$_POST[viewID]' AND `eventID` = '$_POST[id]' AND `sessionID` = '$sesID'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $price = $row['price'] * $row['qty'];
                        $total = $total + $price;
                        $number++;
                }
                if ($_SESSION['discount'] != "") {
                        $today = date("Y-m-d");
                        $sql = "SELECT * FROM `discounts` WHERE `eventID` = '$_POST[id]' AND `code` = '$_SESSION[discount]' AND `expire` > '$today' LIMIT 1";
                        $result = $this->new_mysql($sql);
                        while ($row = $result->fetch_assoc()) {
                                $Dfound = "1";
                                $amount_off = $row['amount_off'];
                                $type = $row['amount_how'];
                        }
                }
                        if ($Dfound == "1") {
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
                $fees = $this->get_fees($total,$number);
                $total = $total + $fees;
                $total = number_format($total,2,'.',',');
                $settings = $this->get_settings();
                include "class/gwapi.class.php";
                $gw = new gwapi;
		$gw->setLogin($settings[10], $settings[11]);
                $name = explode(" ",$_POST['name']);
                $gw->setBilling($name[0],$name[1],"",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['phone'],$_POST['phone'],$_POST['email'],"www.ticketpointe.com");
                $gw->setShipping($name[0],$name[1],"na",$_POST['addr1'],"", $_POST['city'],$_POST['state'],$_POST['zip'],"US",$_POST['email']);
                $ordernumber = rand(50,1000);
                $ip = $_SERVER['REMOTE_ADDR'];
                $gw->setOrder($ordernumber,"TicketePointe",1, 2, $ordernumber,$ip);
                $r = $gw->doSale($total,$_POST['ccNo'],"$_POST[expMonth]$_POST[expYear]",$_POST['cvv']); // amount, CC, EXP MMYY, CVV
                $url = $gw->responses['responsetext'];
                $data =explode("=",$url);
                if ($data[0] == "Approved") { // SUCCESS
			$this->new_order_process();
                } else {
                        print "<br><font color=red>There was an error processing your order. Please try another credit card or call your bank then try again.</font><br><br>";
                        print "Error: $data[0]\n";
			die;
                }
        }

	public function qr_code($sesID,$viewID) {
		$settings = $this->get_settings();

                //print "<div id=\"page_view\">";

		include_once('qr/QRGenerator.php');
		$sql = "
		SELECT 
			`cart`.`description`,
			`cart`.`qty`,
			DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
			`events`.`title`,
			`events`.`start_time`,
			`events`.`end_time`,
			`location`.`location`,
			`cart`.`id`,
			`cart`.`sessionID`,
			`cart`.`viewID`

		FROM 
			`cart`,`events`

		LEFT JOIN `location` ON `events`.`locationID` = `location`.`id`

		WHERE 
			`cart`.`sessionID` = '$sesID' 
			AND `cart`.`viewID` = '$viewID'
			AND `cart`.`eventID` = `events`.`id`
		";


		$result = $this->new_mysql($sql);
		$total = $result->num_rows;		

		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$i++;

			//$qr = "http://" . $settings[8] . "/readqr.php?qr=" . $row['id'] . "-" . $row['sessionID'] . "-" . $row['viewID'];
			//$qrcode = new QRGenerator($qr,100);  // 100 is the qr size
			//$image = $qrcode->generate();

			$html .= "Please print this or show each QR code to the event host.<br><br>";
			$html .= "<b>Event: $row[title]</b><br>Location: $row[location]<br>Valid from $row[start_date] to $row[end_date]<br>Operating Hours: $row[start_time] $row[end_time]<br><br>
			Ticket Type: $row[description]<br>Quantity: $row[qty]<br>";

			for ($i=0; $i < $row['qty']; $i++) {
				$i2 = $i + 1;
	                        $qr = "http://" . $settings[8] . "/readqr.php?qr=" . $row['id'] . "-" . $row['sessionID'] . "-" . $row['viewID']."-" . $i;
        	                $qrcode = new QRGenerator($qr,100);  // 100 is the qr size
                	        $image = $qrcode->generate();
				//$html .= "<br><br>Ticket $i2<br><img src=\"$image\"><br>";
			}
			//$html .= "<img src=\"$image\">";

		}

		return $image;
		//print "</div>";

	}


        public function qr_code_passbook($sesID,$viewID) {
                $settings = $this->get_settings();

                //print "<div id=\"page_view\">";

                $sql = "
                SELECT 
                        `cart`.`description`,
                        `cart`.`qty`,
                        DATE_FORMAT(`events`.`start_date`, '%m/%d/%Y') AS 'start_date',
                        DATE_FORMAT(`events`.`end_date`, '%m/%d/%Y') AS 'end_date',
                        `events`.`title`,
                        `events`.`start_time`,
                        `events`.`end_time`,
                        `location`.`location`,
                        `cart`.`id`,
                        `cart`.`sessionID`,
                        `cart`.`viewID`

                FROM 
                        `cart`,`events`

                LEFT JOIN `location` ON `events`.`locationID` = `location`.`id`

                WHERE 
                        `cart`.`sessionID` = '$sesID' 
                        AND `cart`.`viewID` = '$viewID'
                        AND `cart`.`eventID` = `events`.`id`
                ";


                $result = $this->new_mysql($sql);
                $total = $result->num_rows;

                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $i++;

                        for ($i=0; $i < $row['qty']; $i++) {
                                $i2 = $i + 1;
                                $qr = "http://" . $settings[8] . "/readqr.php?qr=" . $row['id'] . "-" . $row['sessionID'] . "-" . $row['viewID']."-" . $i;
				$qr = $this->encrypt_decrypt('encrypt',$qr);
                                //$html .= "<br><br>Ticket $i2<br><img src=\"$image\"><br>";
                        }
                        //$html .= "<img src=\"$image\">";

                }

                return $qr;
                //print "</div>";

        }




	public function get_form_free($post_to) {
                $settings = $this->get_settings();
                $state = $this->get_states();
		$_SESSION['token'] = rand(50,1000);


                ?>
	    <form id="myCCForm" action="<?=$post_to;?>" method="post">
	        <input type="hidden" name="section" value="free">

		<?php
		foreach ($_POST as $key=>$value){
			if ($key != "section") {
				print "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
			}
		}
		?>


	        <input id="token" name="token" type="hidden" value="<?=$_SESSION['token'];?>">
                                <div class="row top-buffer">
				<div class="col-sm-12">
                                        <input type="text" name="name" placeholder="Your First and Last Name" size=40 required>
				</div>
				</div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <input type="text" name="addr1" placeholder="Address" size=40>
				</div></div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <input type="text" name="city" placeholder="city" size=40>
				</div></div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <select name="state"><?=$state?></select>
				</div></div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <input type="text" name="zip" placeholder="Zip Code" size=40>
				</div></div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <input type="text" name="email" placeholder="Your email address" size=40 required>
				</div></div>
                                <div class="row top-buffer">
                                <div class="col-sm-12">
                                        <input type="text" name="phone" placeholder="Your phone number" size=40>
				</div></div>
				<div class="row top-buffer">
				<div class="col-sm-12">
                    			<input type="submit" value="Complete Order" class="btn btn-primary">
				</div></div>
	</form>
	<?php
	}


	public function get_cc_form($post_to) {
		$settings = $this->get_settings();
		$state = $this->get_states();
		?>


    <form id="myCCForm" action="<?=$post_to;?>" method="post">
	<input type="hidden" name="section" value="payment">

                <?php
                foreach ($_POST as $key=>$value){
                        if ($key != "section") {
                                print "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
                        }
                }
                ?>


				<div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="name" placeholder="Your First and Last Name" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="addr1" placeholder="Address" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="city" placeholder="city" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <select name="state"><?=$state?></select>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="zip" placeholder="Zip Code" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="email" placeholder="Your email address" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
                                        <input type="text" name="phone" placeholder="Your phone number" size=40 required>
				</div></div>
                                <div class="row top-buffer"><div class="col-sm-12">
		                <input id="ccNo" name="ccNo" type="text" size="40" placeholder="Credit Card Number" value="" autocomplete="off" required />
				</div></div>
                                <div class="row top-buffer">
					<div class="col-sm-4">Expiration Date:</div>
					<div class="col-sm-8">
					 <select name="expMonth" id="expMonth">
					<option value="01">Jan - 01</option>
					<option value="02">Feb - 02</option>
					<option value="03">Mar - 03</option>
					<option value="04">Arp - 04</option>
					<option value="05">May - 05</option>
					<option value="06">Jun - 06</option>
					<option value="07">Jul - 07</option>
					<option value="08">Aug - 08</option>
					<option value="09">Sep - 09</option>
					<option value="10">Oct - 10</option>
					<option value="11">Nov - 11</option>
					<option value="12">Dec - 12</option>
					</select>
		                	<span> / </span>

		<?php
		$today = date("y");
		$end = $today + 10;
		for ($i=$today; $i < $end; $i++) {
			$options .= "<option value=\"$i\">$i</option>";
		}

		?>
		<select name="expYear" id="expYear"><?=$options;?></select>
		</div></div>

		<div class="row top-buffer">
		<div class="col-sm-12">
                <input id="cvv" name="cvv" size="40" placeholder="Security code on the back" type="text" value="" autocomplete="off" required />
		</div></div>
		<div class="row top-buffer"><div class="col-sm-12">
	            <input type="submit" value="Submit Payment" class="btn btn-primary">
		</div></div>
        </form>



		<?
	}

	public function social_link($type,$url,$event,$tagline) {

		switch ($type) {
			case "facebook":
			print "<a href=\"http://www.facebook.com/sharer.php?u=$url\" target=_blank><img src=\"img/facebook.png\" alt=\"Facebook\"></a>";
			break;
			case "google":
		    	print "<a href=\"https://plus.google.com/share?url=$url\" target=\"_blank\"><img src=\"img/google.png\" alt=\"Google\" /></a>";
			break;

			case "linkedin":
			print "<a href=\"http://www.linkedin.com/shareArticle?mini=true&amp;url=$url\" target=\"_blank\"><img src=\"img/linkedin.png\" alt=\"LinkedIn\" /></a>";
			break;

			case "tumbler":
			print "<a href=\"http://www.tumblr.com/share/link?url=$url&amp;title=$event\" target=\"_blank\"><img src=\"img/tumblr.png\" alt=\"Tumblr\" /></a>";
			break;

			case "twitter":
			print "<a href=\"https://twitter.com/share?url=$url&amp;text=$event&amp;hashtags=$tagline\" target=\"_blank\"><img src=\"img/twitter.png\" alt=\"Twitter\" /></a>";
			break;
		}
	}

	public function check_business_access() {
		$sql = "
		SELECT
			`id`
		FROM
			`users`
		WHERE
			`users`.`id` = '$_SESSION[id]'
			AND `users`.`account_type` = '2'
			AND `users`.`resellerID` = '0'
		";
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

	public function new_user() {
                $check_reseller = $this->check_business_access();
                if ($check_reseller == "FALSE") {
                        print "<br><font color=red>Access Denied</font><br>";
                        die;
                }

		print "<h2>Users : New User</h2>";

		print "<form action=\"index.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"save_new_user\">
		<table class=\"table\">
		<tr><td>Username:</td><td><input type=\"text\" name=\"uuname\" size=20></td></tr>
		<tr><td>Password:</td><td><input type=\"text\" name=\"uupass\" size=20></td></tr>
		<tr><td>Email:</td><td><input type=\"text\" name=\"email\" size=20></td></tr>
		<tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" size=20></td></tr>
		<tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" size=20></td></tr>
		<tr><td>Event Details Access (required)</td><td><select name=\"event_details\"><option>Yes</option></select></td></tr>
		<tr><td>Event Design Access?</td><td><select name=\"event_design\"><option>No</option><option>Yes</option></select></td></tr>
		<tr><td>Social Access?</td><td><select name=\"social\"><option>No</option><option>Yes</option></select></td></tr>
		<tr><td>Event Settings Access?</td><td><select name=\"event_settings\"><option>No</option><option>Yes</option></select></td></tr>
		<tr><td>Create Tickets Access?</td><td><select name=\"create_tickets\"><option>No</option><option>Yes</option></select></td></tr>
		<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Create User\"></td></tr>
		</table>
		</form>";
	}

	public function save_new_user() {
                $check_reseller = $this->check_business_access();
                if ($check_reseller == "FALSE") {
                        print "<br><font color=red>Access Denied</font><br>";
                        die;
                }

                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <div class="col-md-12 col-lg-6" id="ajax">
                ';
                print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';

		// check for errors
		$sql = "SELECT `uuname` FROM `users` WHERE `uuname` = '$_POST[uuname]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$err = "1";
		}

                $sql = "SELECT `email` FROM `users` WHERE `email` = '$_POST[email]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
                        $err = "2";
                }

		if ($_POST['uuname'] == "") {
			$err = "3";
		}
		if ($_POST['email'] == "") {
			$err = "4";
		}
		if ($_POST['uupass'] == "") {
			$err = "5";
		}

		switch ($err) {
			case "1":
			print "<br><font color=red>The username <b>$_POST[uuname]</b> is not available. Please click back and try again.</font><br>";
			die;
			break;

			case "2":
			print "<br><font color=red>The email <b>$_POST[email]</b> is not available. Please click back and try again.</font><br>";
			die;
			break;

			case "3":
			print "<br><font color=red>The username can not be blank. Please click back and try again.</font><br>";
                        die;
                        break;

			case "4":
			print "<br><font color=red>The email can not be blank. Please click back and try again.</font><br>";
                        die;
                        break;

			case "5":
			print "<br><font color=red>The password can not be blank. Please click back and try again.</font><br>";
                        die;
                        break;


			default:
			// ok
			$sql = "INSERT INTO `users` (`uuname`,`uupass`,`email`,`active`,`verified`,`account_type`,`fname`,`lname`,`resellerID`,`event_details`,`event_design`,`social`,`event_settings`,`create_tickets`) VALUES
			('$_POST[uuname]','$_POST[uupass]','$_POST[email]','Yes','Yes','2','$_POST[fname]','$_POST[lname]','$_SESSION[id]','$_POST[event_details]','$_POST[event_design]','$_POST[social]','$_POST[event_settings]','$_POST[create_tickets]')";
			$result = $this->new_mysql($sql);
			if ($result == "TRUE") {
				$msg = "<font color=green><br>The user was created<br><br></font>";
			} else {
				$msg = "<font color=red><br>The user failed to create.<br><br></font>";
			}
			break;
		}

		$this->users($msg);
		print "</div></div></div></div>";

	}

	public function edit_user() {
                if ($_SESSION['id'] == "") {
                        print "<br><font color=red>Please log back in.</font><br>";
                        die;
                }

                $check_reseller = $this->check_business_access();
                if ($check_reseller == "FALSE") {
                        print "<br><font color=red>Access Denied</font><br>";
                        die;
                }

                print "<h2>Users : Edit User</h2>";

		$sql = "SELECT * FROM `users` WHERE `id` = '$_GET[id]' AND `resellerID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {

	                print "<form action=\"index.php\" method=\"post\">
        	        <input type=\"hidden\" name=\"section\" value=\"save_update_user\">
			<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">
	                <table class=\"table\">
        	        <tr><td>Username:</td><td>$row[uuname]</td></tr>
	                <tr><td>Email:</td><td><input type=\"text\" name=\"email\" value=\"$row[email]\"size=20></td></tr>
        	        <tr><td>First Name:</td><td><input type=\"text\" name=\"fname\" value=\"$row[fname]\" size=20></td></tr>
                	<tr><td>Last Name:</td><td><input type=\"text\" name=\"lname\" value=\"$row[lname]\" size=20></td></tr>
	                <tr><td>Event Details Access (required)</td><td><select name=\"event_details\"><option selected>$row[event_details]</option><option>Yes</option></select></td></tr>
        	        <tr><td>Event Design Access?</td><td><select name=\"event_design\"><option selected>$row[event_design]</option><option>No</option><option>Yes</option></select></td></tr>
                	<tr><td>Social Access?</td><td><select name=\"social\"><option selected>$row[social]</option><option>No</option><option>Yes</option></select></td></tr>
	                <tr><td>Event Settings Access?</td><td><select name=\"event_settings\"><option selected>$row[event_settings]</option><option>No</option><option>Yes</option></select></td></tr>
        	        <tr><td>Create Tickets Access?</td><td><select name=\"create_tickets\"><option selected>$row[create_tickets]</option><option>No</option><option>Yes</option></select></td></tr>
                	<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Update User\"></td></tr>
	                </table>
        	        </form>";
			$found = "1";
		}
		if ($found != "1") {
			print "<br><font color=red>Unknown error.</font><br>";
		}

	}

	public function save_update_user() {

                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <div class="col-md-12 col-lg-6" id="ajax">
                ';
                print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';

		// check email
		$sql = "SELECT `email` FROM `users` WHERE `email` = '$_POST[email]' AND `id` != '$_POST[id]'";
                $result = $this->new_mysql($sql);
                while ($row = $result->fetch_assoc()) {
			$found = "1";
		}
		if ($found == "1") {
			print "<br><font color=red>Sorry, <b>$_POST[email]</b> is already registered with another user.</font><br>";
			die;
		}
		$sql = "UPDATE `users` SET `email` = '$_POST[email]', `fname` = '$_POST[fname]', `lname` = '$_POST[lname]', `event_details` = '$_POST[event_details]', 
		`event_design` = '$_POST[event_design]', `social` = '$_POST[social]', `event_settings` = '$_POST[event_settings]', 
		`create_tickets` = '$_POST[create_tickets]' WHERE `id` = '$_POST[id]' AND `resellerID` = '$_SESSION[id]'";
                $result = $this->new_mysql($sql);
                if ($result == "TRUE") {
                        $msg = "<font color=green><br>The user was updated<br><br></font>";
                } else {
                        $msg = "<font color=red><br>The user failed to update.<br><br></font>";
                }
		$this->users($msg);
		print "</div></div></div></div>";

	}

    public function users($msg='') {
        $device = $this->device_type();
        if ($device == "0") {
            $this->users_desktop($msg);
        } else {
            $this->users_mobile($msg);
        }
    }

	public function users_desktop($msg='') {

		if ($_SESSION['id'] == "") {
			print "<br><font color=red>Please log back in.</font><br>";
			die;
		}

		$check_reseller = $this->check_business_access();
		if ($check_reseller == "FALSE") {
			print "<br><font color=red>Access Denied</font><br>";
			die;
		}

                                print "
                                <div class=\"page-head\">
                                        <h2 class=\"page-head-title\"><h2>Users <input type=\"button\" class=\"btn btn-success\" value=\"Add New User\" onclick=\"document.location.href='index.php?section=dashboard&center=new_user'\"></h2>
                                </div>
                                ";

		print "$msg";

		if ($_GET['act'] == "delete") {
			$sql = "DELETE FROM `users` WHERE `id` = '$_GET[id]' AND `resellerID` = '$_SESSION[id]'";
			$result = $this->new_mysql($sql);
			print "<br><font color=green>The user was deleted.<br><br></font><br>";
		}



		print '
        <div class="main-content container-fluid">
          <div class="row">
              <div class="col-sm-6">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Current Users
                </div>
                <div class="panel-body">
                  <table class="table table-striped table-borderless">
                    <thead>


		<tr>
			<th><b>Name</b></th>
			<th><b>Username</b></th>
			<th><b>Details</b></th>
			<th><b>Design</b></th>
			<th><b>Social</b></th>
			<th><b>Settings</b></th>
			<th><b>Tickets</b></th>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody class="no-border-x">
		';
		$sql = "SELECT * FROM `users` WHERE `resellerID` = '$_SESSION[id]' ORDER BY `lname` ASC, `fname` ASC";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			print "<tr>
			<td>$row[fname] $row[lname]</td>
			<td>$row[uuname]</td>
			<td>$row[event_details]</td>
			<td>$row[event_design]</td>
			<td>$row[social]</td>
			<td>$row[event_settings]</td>
			<td>$row[create_tickets]</td>
			<td>
				<input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" 
				onclick=\"if(confirm('WARNING: You are about to delete $row[fname] $row[lname]')){document.location.href='index.php?section=dashboard&center=users&act=delete&id=$row[id]'};\">&nbsp;
				<input type=\"button\" class=\"btn btn-primary\" value=\"Edit\"
				onclick=\"document.location.href='index.php?section=dashboard&center=edit_user&id=$row[id]'\">
			</td>
			</tr>
			";
			$found = "1";
		}

		if ($found != "1") {
			print "<tr><td colspan=8><center><font color=blue>You do not have any users.</font></center></td></tr>";
		}
		print "</tbody></table>";
                print "</div></div></div></div></div>";


	}

    public function users_mobile($msg='') {

        if ($_SESSION['id'] == "") {
            print "<br><font color=red>Please log back in.</font><br>";
            die;
        }

        $check_reseller = $this->check_business_access();
        if ($check_reseller == "FALSE") {
            print "<br><font color=red>Access Denied</font><br>";
            die;
        }

        print "<h2>Users</h2>";

        print "$msg";

        if ($_GET['act'] == "delete") {
            $sql = "DELETE FROM `users` WHERE `id` = '$_GET[id]' AND `resellerID` = '$_SESSION[id]'";
            $result = $this->new_mysql($sql);
            print "<br><font color=green>The user was deleted.<br><br></font><br>";
        }

        print "<input type=\"button\" class=\"btn btn-success\" value=\"Add New User\" onclick=\"document.location.href='index.php?section=dashboard&center=new_user'\"><br><hr>";

        print "<table class=\"table\">";

        $sql = "SELECT * FROM `users` WHERE `resellerID` = '$_SESSION[id]' ORDER BY `lname` ASC, `fname` ASC";
        $result = $this->new_mysql($sql);
        while ($row = $result->fetch_assoc()) {
            print "
            <tr>
                <td colspan=2><h3>$row[fname] $row[lname]</h3></td>
            </tr>
            <tr>
                <td><b>Details:</b></td>
                <td>$row[event_details]</td>
            </tr>
            <tr>
                <td><b>Design:</b></td>
                <td>$row[event_design]</td>
            </tr>
            <tr>
                <td><b>Social:</b></td>
                <td>$row[social]</td>
            </tr>
            <tr>
                <td><b>Settings:</b></td>
                <td>$row[event_settings]</td>
            </tr>
            <tr>
                <td><b>Tickets:</b></td>
                <td>$row[create_tickets]</td>
            </tr>
            <tr>
            <td>
                <input type=\"button\" class=\"btn btn-danger\" value=\"Delete\" 
                onclick=\"if(confirm('WARNING: You are about to delete $row[fname] $row[lname]')){document.location.href='index.php?section=dashboard&center=users&act=delete&id=$row[id]'};\">&nbsp;
                <input type=\"button\" class=\"btn btn-primary\" value=\"Edit\"
                onclick=\"document.location.href='index.php?section=dashboard&center=edit_user&id=$row[id]'\">
            </td>
            </tr>
            <tr><td><hr></td></tr>";
            $found = "1";
        }

        if ($found != "1") {
            print "<tr><td colspan=2><center><font color=blue>You do not have any users.</font></center></td></tr>";
        }
        print "</table>";

    }

	public function registration_form() {
		print "<h2>Registration Form</h2>";

		$sql = "SELECT * FROM `registration` WHERE `eventID` = '$_GET[id]' AND `userID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		$row = $result->fetch_assoc();

		print "The registration form will allow up to 10 fields to be collected. You must complete at least 1 field. Any field you do you want to collect simply leave the field blank.<br><b>Note: updating or altering your form will remove any past registration data.</b><br><br>";

		print "<br><a href=\"downloadr.php?id=$_GET[id]\" target=_blank class=\"btn btn-warning\">Download Registration Data</a><br><br>";

		print "<form action=\"index.php\" method=\"post\">
		<input type=\"hidden\" name=\"section\" value=\"save_registration_form\">
		<input type=\"hidden\" name=\"eventID\" value=\"$_GET[id]\">
		<table class=\"table\">
		";

		for ($x=1; $x < 11; $x++) {
			$y = "t";
			$y.= $x;
			print "
			<tr>
				<td>Field $x:</td><td><input type=\"text\" name=\"$y\" value=\"$row[$y]\" size=30></td>
			</tr>";
		}
		print "<tr><td colspan=2><input type=\"submit\" class=\"btn btn-primary\" value=\"Save\"></td></tr>";
		print "</table></form>";
		

	}

	public function save_registration_form() {
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <div class="col-md-12 col-lg-6" id="ajax">
                ';
                print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';

		$sql = "DELETE FROM `registration` WHERE `eventID` = '$_POST[eventID]' AND `userID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);

		$sql = "INSERT INTO `registration` 
		(`eventID`,`userID`,`t1`,`t2`,`t3`,`t4`,`t5`,`t6`,`t7`,`t8`,`t9`,`t10`) 
		VALUES
		('$_POST[eventID]','$_SESSION[id]','$_POST[t1]','$_POST[t2]','$_POST[t3]','$_POST[t4]','$_POST[t5]','$_POST[t6]','$_POST[t7]','$_POST[t8]','$_POST[t9]','$_POST[t10]')";

		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			print "<br><font color=green>The registration form has been saved. You can now close this window.<br><br>If you did not save on the event details page please do so.</font><br>";
		} else {
			print "<br><font color=red>There was an error saving the form.</font><br>";
		}

		print "</div></div></div></div>";
	}

	public function checkin_users() {
        $device = $this->device_type();

		if ($_SESSION['id'] == "") {
			print "<br><font color=red>Your session has timed out. Please log back in.</font><bR>";
			die;
		}


                                print '
                                <div class="page-head">
                                        <h2 class="page-head-title"><h2>Check-In Users</h2>
					This section is used to create users for the Ticket Point Check-In app to be used at the event. You can add a user or delete a user. If you need to edit a user you would simple delete then re-add the user.<br>
                                </div>
                                ';


            print "<form action=\"index.php\" method=\"post\">
            <input type=\"hidden\" name=\"section\" value=\"create_checkin_user\">

		<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\">First Name</label>
		<div class=\"col-sm-6\"><input type=\"text\" name=\"firstname\" style=\"width:200px\" required class=\"form-control\"></div>
		</div>
		<br><br>

		<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\">Last Name</label>
		<div class=\"col-sm-6\"><input type=\"text\" name=\"lastname\" style=\"width:200px\" required class=\"form-control\"></div>
		</div>
		<br><br>
		<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\">Phone Number</label>
		<div class=\"col-sm-6\"><input type=\"text\" name=\"phonenumber\" class=\"form-control\" style=\"width:200px\"></div>
		</div>
		<br><br>

                <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">Email</label>
                <div class=\"col-sm-6\"><input type=\"text\" name=\"email\" class=\"form-control\" style=\"width:200px\"></div>
                </div>
		<br><br>

                <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">Password</label>
                <div class=\"col-sm-6\"><input type=\"text\" name=\"password\" class=\"form-control\" style=\"width:200px\"></div>
                </div>
		<br><br>

		<div class=\"form-group\">
		<label class=\"col-sm-3 control-label\"></label>
		<div class=\"col-sm-6\"><input type=\"submit\" value=\"Create User\" class=\"btn btn-success\" style=\"width:200px\"></div>
		</div>
		<br><br>
            </form>
            ";     
       
		$sql = "SELECT * FROM `checkin_users` WHERE `resellerID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);

		print '
        <div class="main-content container-fluid">
          <div class="row">
	      <div class="col-sm-6">
              <div class="panel panel-default panel-table">
                <div class="panel-heading">Current Users
                </div>
                <div class="panel-body">
                  <table class="table table-striped table-borderless">
                    <thead>
			<tr>
                        <th style="width:50%;">Name</th>
                        <th style="width:25%;">Email</th>
                        <th class="actions"></th>
			</tr>
                    </thead>
                    <tbody class="no-border-x">
		';

		while ($row = $result->fetch_assoc()) {
			print "<tr><td>$row[firstname] $row[lastname]</td><td>$row[email]</td><td class=\"actions\">
			<a href=\"index.php?section=dashboard&center=delete_cu&id=$row[id]\" class=\"icon\"><i class=\"mdi mdi-delete\"></i></a>
			</td></tr>";
			$f = "1";
		}
		if ($f != "1") {
			print "<tr><td colspan=3><font color=blue>You do not have any Check-In users. Please add at least one.</font></td></tr>";
		}
		print "</tbody></table>";
		print "</div></div></div></div></div>";

	}

	public function delete_checked_user() {

                if ($_SESSION['id'] == "") {
                        print "<br><font color=red>Your session has timed out. Please log back in.</font><bR>";
                        die;
                }

		$sql = "SELECT * FROM `checkin_users` WHERE `id` = '$_GET[id]' AND `resellerID` = '$_SESSION[id]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$sql2 = "DELETE FROM `checkin_users` WHERE `id` = '$_GET[id]' AND `resellerID` = '$_SESSION[id]'";
			$result2 = $this->new_mysql($sql2);

                        $DB2_NAME = 'wwsib2_siberian';
                        $DB2_HOST = '98.142.210.130';
                        $DB2_USER = "wwsib2_tp";
                        $DB2_PASS = '2q,wJ[IIgIL)';
                        $linkID2 = new mysqli($DB2_HOST, $DB2_USER, $DB2_PASS, $DB2_NAME);
                        $sql_siberian = "DELETE FROM `customer` WHERE `email` = '$row[email]' AND `app_id` = '25'";

                        //print "Test: $sql_siberian<br>";

                        $result2 = $linkID2->query($sql_siberian);
			print "<br><font color=green>The user was removed.</font><br>";
		}
                $this->checkin_users();
	}

	public function create_checkin_user() {
                $this->navigation2();
                print '
                      <div class="be-content">
                        <div class="main-content container-fluid">
                          <div class="row">
                            <!--<div class="col-md-12 col-lg-6" id="ajax">-->
                                <div id="ajax">
                ';

                //print '<div class="row"><div class="col-md-8"><div class="row"><div class="col-md-8" id="ajax">';

		$sql = "SELECT * FROM `checkin_users` WHERE `email` = '$_POST[email]'";
		$result = $this->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$found = "1";
		}
		if ($found == "1") {
			print "<br><font color=red>Sorry, the email $_POST[email] is already registered.</font><br><br>";
			die;
		}

		$new_pw = sha1($_POST['password']);
		$sql = "INSERT INTO `checkin_users` (`resellerID`,`firstname`,`lastname`,`email`,`password`) VALUES ('$_SESSION[id]','$_POST[firstname]','$_POST[lastname]','$_POST[email]','$new_pw')";
		$result = $this->new_mysql($sql);
		if ($result == "TRUE") {
			// inject into Siberian CMS

                        $DB2_NAME = 'wwsib2_siberian';
                        $DB2_HOST = '98.142.210.130';
                        $DB2_USER = "wwsib2_tp";
                        $DB2_PASS = '2q,wJ[IIgIL)';
			$linkID2 = new mysqli($DB2_HOST, $DB2_USER, $DB2_PASS, $DB2_NAME);
			$sql_siberian = "INSERT INTO `customer` (`app_id`,`firstname`,`lastname`,`phonenumber`,`email`,`password`,`can_access_locked_features`,`is_active`,`created_at`,`updated_at`) VALUES
			('25','$_POST[firstname]','$_POST[lastname]','$_POST[phonenumber]','$_POST[email]','$new_pw','1','1',NOW(),NOW())";

			//print "Test: $sql_siberian<br>";

			$result2 = $linkID2->query($sql_siberian);
			if ($result2 != "TRUE") {
				print "<br><font color=red>The local user was created but there was an error creating the app user. Please contact support.</font><br>";
			}


			print "<br><font color=green>The user was created.</font><br>";
		} else {
			print "<br><font color=red>There was an error creating the user.</font><br>";
		}
		$this->checkin_users();	
                print '</div></div></div></div>';
	}

	public function navigation2() {
		$file = "templates/desktop/hq_nav.phtml";
		if (file_exists($file)) {
                        include "$file";
                }


	}


        public function navigation() {

		if ($_SESSION['resellerID'] == "0") {
			$event_details = "Yes";
			$event_design = "Yes";
			$social = "Yes";
			$event_settings = "Yes";
			$create_tickets = "Yes";
			$checkin = "Yes";	

		} else {
			$event_details = $_SESSION['event_details'];
			$event_design = $_SESSION['event_design'];
			$social = $_SESSION['social'];
			$event_settings = $_SESSION['event_settings'];
			$create_tickets = $_SESSION['create_tickets'];
			$_SESSION['id'] = $_SESSION['resellerID'];
		}

                ?>


                <div id="dashboard_left">

                <form name="myform">

                <button type="button" class="btn btn-primary btn-lg" onclick="load_profile(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>My Profile</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

		<?php
		if ($checkin == "Yes") {
		?>
		<br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="load_checkin(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Check-In Users</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>


		<?php
		}
		?>

		<?php
		if ($event_details == "Yes") {
		?>
                <br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="load_details(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Details</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>
		<?php
		}

		if ($event_design == "Yes") {
		?>
                <br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="load_design(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Design</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>
		<?php
		}

		if ($social == "Yes") {
		?>
                <br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="social(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Social</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>
		<?php
		}

		if ($event_settings == "Yes") {
		?>
                <br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="settings(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Settings</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>
		<?php
		}

		if ($create_tickets == "Yes") {
		?>
                <br><br>
                <button type="button" class="btn btn-primary btn-lg" onclick="tickets(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Create Tickets</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>
		<?php
		}
		?>

		<?php
		if (($_SESSION['account_type'] == "2") && ($_SESSION['resellerID'] == "0")) {
		?>

		<br><br>

                <button type="button" class="btn btn-primary btn-lg" onclick="users(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Manage Users</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

		<?php
		}
		?>


                </form>
                </div>

<!-- scripts -->
<script>
function logout(myform) {
        $.get('ajax/load.php?type=logout',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}


function load_profile(myform) {
        $.get('ajax/load.php?type=profile',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function load_checkin(myform) {
        $.get('ajax/load.php?type=checkin',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}


function load_details(myform) {
        $.get('ajax/load.php?type=details',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}


function load_design(myform) {
        $.get('ajax/load.php?type=design',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function new_design(myform) {
        $.get('ajax/load.php?type=new_design',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function new_details(myform) {
        $.get('ajax/load.php?type=new_details',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function settings(myform) {
        $.get('ajax/load.php?type=settings',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function tickets(myform) {
        $.get('ajax/load.php?type=tickets',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function social(myform) {
        $.get('ajax/load.php?type=social',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

function users(myform) {
        $.get('ajax/load.php?type=users',
        $(myform).serialize(),
        function(php_msg) {
                $("#dashboard_right").html(php_msg);
        });
}

</script>


                <?php
        }


}
}
?>
