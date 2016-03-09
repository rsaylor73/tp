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

$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
$template->load_template($file,$null);

if ($_POST['lg'] == "login") {
	$sql = "SELECT * FROM `users` WHERE `uuname` = '$_POST[uuname]' AND BINARY `uupass` = '$_POST[uupass]'";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
        	foreach ($row as $key=>$value) {
                	$_SESSION[$key] = $value;
	        }
        	$_SESSION['id2'] = $row['id'];
	        $_SESSION['logged'] = "TRUE";
        	$ok = "1";
	        if ($_POST['rememberme'] == "checked") {
        	        setcookie('uuname', $_POST['uuname'], time() + (86400 * 30), "/"); // 86400 = 1 day
                	setcookie('rememberme', $_POST['rememberme'], time() + (86400 * 30), "/"); // 86400 = 1 day
	        }

	        print "<div class=\"modal-body\"><br><br><br><br><font color=green>Login sucessfull. Loading please wait...</font><br><bR></div>";

	        ?>
	        <script>
        	setTimeout(function()
	        {
        	        window.location.replace('index.php?section=dashboard')
	        }
        	,2000);

	        </script>
	        <?php
		$found = "1";
		die;
	}
	if ($found != "1") {
		$msg = "<br><center><font color=red>Login was incorrect.</font></center><br>";
	}
}


?>
<style>
input[type=text], select {
    width: 75%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=password], select {
    width: 75%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
input[type=submit] {
    width: 100%;
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover {
    background-color: #45a049;
}

</style>
<div id="page_view" style="text-align:center">
<h2>Login</h2>

                        <div id="login">
			<?=$msg;?>
                        <form name="myform" action="login.php" method="post">
			<input type="hidden" name="lg" value="login"> 
                               <div class="modal-body">
                                <table class="table">
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
                                <td colspan=2 align="center"><input type="submit" class="btn btn-primary btn-lg" value="Login"></td>
                                </tr>
                                </table>
                                </div>
                        </form>
                        </div>


</div>

                        <script>
                                 function check_uu(myform) {
                                        $.get('ajax/checkuu.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#checkuu").html(php_msg);
                                        });
                                 }
                        
                                 function check_email(myform) {
                                        $.get('ajax/checkemail.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#checkemail").html(php_msg);
                                        });
                                 }

                                 function check_pw(myform) {
                                        $.get('ajax/checkpw.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#checkpw").html(php_msg);
                                        });
                                 }
                                
                                 function register(myform) {
                                        $.get('ajax/register.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#registration").html(php_msg);
                                        });
                                 }
                                 function login(myform) {
                                        $.get('ajax/login.php',
                                        $(myform).serialize(),
                                        function(php_msg) {
                                                $("#login").html(php_msg);
                                        });
                                 }
                                
                        </script>


<?php

$file = $GLOBAL['path']  . "/templates/" . $dir . "/footer.phtml";
$template->load_template($file,$null);
?>
