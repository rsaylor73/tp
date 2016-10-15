<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

/*
if ($_SERVER['HTTP_HOST'] != $GLOBAL['domain']) {
        $sub = explode(".",$_SERVER['HTTP_HOST']);
        $redirect = "http://".$GLOBAL['domain']."/".$sub[0];
        header("Location: $redirect");
}
*/

// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}

//$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
//$file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_header.phtml";
//$template->load_template($file,$null);
//$device = $tickets->device_type();


print '
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/img/logo-fav.png">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
  </head>
';

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
 <body class="be-splash-screen">


    <div class="be-wrapper be-login">
      <div class="be-content">
        <div class="main-content container-fluid">
          <div class="splash-container">
            <div class="panel panel-default panel-border-color panel-border-color-primary">
              <div class="panel-heading"><img src="assets/img/tp.png" alt="logo" width="100" height="100" class="logo-img"><span class="splash-description">Please enter your user information.
		<?php
		if ($_GET['p'] == "1") {
			print "<br><font color=green>Your login details was sent to your email.</font>";
		}
		?>
		</span></div>
              <div class="panel-body" id="ajax">


                        <form name="myform" action="login.php" method="post">
                        <input type="hidden" name="lg" value="login">

                  <div class="form-group">

			<input type="text" name="uuname" id="uuname" value="<?=$_COOKIE['uuname'];?>" placeholder="Username" autocomplete="off" class="form-control">
                  </div>
                  <div class="form-group">
			<input type="password" name="uupass" id="uupass" placeholder="Password" class="form-control">
                  </div>
                  <div class="form-group row login-tools">
                    <div class="col-xs-6 login-remember">
                      <div class="be-checkbox">
			<input type="checkbox" name="rememberme" value="checked" <?=$_COOKIE['rememberme'];?> 
			onclick="return confirm('By clicking OK you understand this will place a cookie on your machine. If this is a public computer you should click cancel.')">
                        <label for="remember">Remember Me</label>
                      </div>
                    </div>
                    <div class="col-xs-6 login-forgot-password"><a href="forgot_password.php">Forgot Password?</a></div>
                  </div>
                  <div class="form-group login-submit">
                    <button data-dismiss="modal" type="submit" class="btn btn-primary btn-xl">Sign me in</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="splash-footer"><a href="https://ticketpointe.com">Ticket Pointe Home</a></div>
            <div class="splash-footer"><span>Don't have an account? <a href="register">Sign Up</a></span></div>
            <br>
            <div class="splash-footer">&copy; <?php $date=date("Y"); echo $date;?> Ticket Pointe, LLC</div>

            <div class="splash-footer">

            <span id="siteseal"><script async type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=W0uJ3TMW8lEDbovKEgvO4h3tI0sNaCczq8Af5K245MIrqkX5uhKmYJov4aG2"></script></span>

            </div>

          </div>
        </div>
      </div>
    </div>
    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="assets/js/main.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function(){
      	//initialize the javascript
      	App.init();
      });
      
    </script>
  </body>
</html>










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

//$file = $GLOBAL['path']  . "/templates/" . $dir . "/footer.phtml";
print '</div></div></div></div>';
$file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_footer.phtml";
$template->load_template($file,$null);
?>
