<?php
session_start();
// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}

if ($_GET['section'] == "") {
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
	$template->load_template($file,$null);
}

if ($_GET['section'] == "") {
	print "<div id=\"page_view\">";
	print "<h2>Forgot Password</h2>";
}

if (($_GET['section'] == "") and ($_POST['section'] == "")) {

		$_SESSION['rand1'] = rand(5,10);
		$_SESSION['rand2'] = rand(5,10);


		print "
                <div align=\"center\" id=\"login-scr\">
                <form name=\"myform\" id=\"myform\">
		<input type=\"hidden\" name=\"section\" value=\"send_pw\">
                <table border=0 width=700>
                <tr><td>
                        <table border=0 width=700>
                                <tr><td>Email Address:</td><td><input type=\"text\" name=\"email\" size=20></td></tr>
                                <tr><td>What is $_SESSION[rand1] plus $_SESSION[rand2] ?</td><td><input type=\"text\" name=\"security\" size=20></td></tr>
                                <tr><td>&nbsp;</td><td><input type=\"button\" value=\"Send Password\" class=\"btn btn-primary\" onclick=\"forgot_password(this.form)\"></td></tr>
                        </table>
                </td></tr>
                </table>
                </form>
                </div>";

		?>
                                <script>
                                 function forgot_password(myform) {
                                        $.get('forgot_password.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#login-scr").html(php_msg);
                                        });
                                 }
				</script>

		<?php
}

if ($_GET['section'] == "send_pw") {

	$answer = $_SESSION['rand1'] + $_SESSION['rand2'];
	if ($answer != $_GET['security']) {
		$err1 = "1";
		$err1_d = "The security question was incorrect";
	}

	if ($err1 != "1") {
		$sql = "
		SELECT
			`users`.`email`,
			`users`.`fname`,
			`users`.`lname`,
			`users`.`uuname`,
			`users`.`uupass`

		FROM
			`users`

		WHERE
			`users`.`email` = '$_GET[email]'

		";
		$settings = $tickets->get_settings();
		$result = $tickets->new_mysql($sql);
		while ($row = $result->fetch_assoc()) {
			$found = "1";
			$subj = "Forgot password on $settings[0]";
			$msg = "$row[fname],<br>You have requested your username and password to be sent to your registered email address.<br><br>
			Username: $row[uuname]<br>
			Password: $row[uupass]<br><br>
			To log into your account please visit $settings[1]";		

                        mail($_GET['email'],$subj,$msg,$settings[3]);
			print "<font color=green>Your login details has been sent to your registered email address.</font><br>";
	
		}
	}

	if (($err1 == "1") or ($found != "1")) {
                $_SESSION['rand1'] = rand(5,10);
                $_SESSION['rand2'] = rand(5,10);


                print "
                <div align=\"center\" id=\"login-scr\">
                <form name=\"myform\" id=\"myform\">
                <input type=\"hidden\" name=\"section\" value=\"send_pw\">
		<font color=red>Either the email was incorrect or the security answer was wrong.</font><br>
                <table border=0 width=700>
                <tr><td>
                        <table border=0 width=700>
                                <tr><td>Email Address:</td><td><input type=\"text\" name=\"email\" size=20></td></tr>
                                <tr><td>What is $_SESSION[rand1] plus $_SESSION[rand2] ?</td><td><input type=\"text\" name=\"security\" size=20></td></tr>
                                <tr><td>&nbsp;</td><td><input type=\"button\" value=\"Send Password\" onclick=\"forgot_password(this.form)\"></td></tr>
                        </table>
                </td></tr>
                </table>
                </form>
                </div>";

                ?>
                                <script>
                                 function forgot_password(myform) {
                                        $.get('forgot_password.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#login-scr").html(php_msg);
                                        });
                                 }
                                </script>

                <?php

	}

}
if ($_GET['section'] == "") {
	print "</div>";
}

if ($_GET['section'] == "") {
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/footer.phtml";
	$template->load_template($file,$null);
}
?>
