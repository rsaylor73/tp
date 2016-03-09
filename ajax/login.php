<?php
session_start();

$sesID = session_id();
// init
include_once "../include/settings.php";
include_once "../include/mysql.php";

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

$sql = "SELECT * FROM `users` WHERE `uuname` = '$_GET[uuname]' AND BINARY `uupass` = '$_GET[uupass]'";
$result = $tickets->new_mysql($sql);
while ($row = $result->fetch_assoc()) {
	foreach ($row as $key=>$value) {
		$_SESSION[$key] = $value;
	}
	$_SESSION['id2'] = $row['id'];
	$_SESSION['logged'] = "TRUE";
	$ok = "1";
	if ($_GET['rememberme'] == "checked") {
		setcookie('uuname', $_GET['uuname'], time() + (86400 * 30), "/"); // 86400 = 1 day
		setcookie('rememberme', $_GET['rememberme'], time() + (86400 * 30), "/"); // 86400 = 1 day
	}

	print "<div class=\"modal-body\"><br><br><font color=green>Login sucessfull. Loading please wait...</font><br><bR></div>";

	?>
	<script>
        setTimeout(function()
        {
	        window.location.replace('index.php?section=dashboard')
        }
        ,2000);

	</script>
	<?php

}

if ($ok != "1") {
	?>
	                        <form name="myform">
                                <div class="modal-body">
                                <table class="table">
				<tr><td colspan=2 align="center"><font color=red>Sorry, the username and or password was incorrect.</font></td></tr>
                                <tr>
                                <td>Username: <div id="checkuu" style="display:inline"></div></td>
                                <td>Password: <div id="checkemail" style="display:inline"></div></td>
                                </tr>
                                <tr>
                                <td><input type="text" name="uuname" id="uuname" size=30 value="<?=$_COOKIE['uuname'];?>"></td>
                                <td><input type="password" name="uupass" id="uupass" size=30></td>
                                </tr>
                                <tr>
                                <td colspan=2 align="center"><input type="checkbox" name="rememberme" value="checked" <?=$_COOKIE['rememberme'];?> onclick="return confirm('By clicking OK you understand this will place a cookie on your machine. If this is a public computer you should click cancel.')"> Remember Me</td>
                                </tr>
                                <tr>
                                <td colspan=2 align="center"><a href="forgot_password.php">Forgot Password?</a></td>
                                </tr>
                                <tr>
                                <td colspan=2 align="center"><input type="button" class="btn btn-primary btn-lg" value="Login" onclick="login(this.form)"></td>
                                </tr>
                                </table>
                                </div>
				</form>
	<?php
}
