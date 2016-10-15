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
	//$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
	//$template->load_template($file,$null);
}

if ($_GET['section'] == "") {
	//print "<div id=\"page_view\">";
	//print "<h2>Forgot Password</h2>";
}

if (($_GET['section'] == "") and ($_POST['section'] == "")) {

		$_SESSION['rand1'] = rand(5,10);
		$_SESSION['rand2'] = rand(5,10);

		/* ----------------------- */
		?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/img/logo-fav.png">
    <title>Forgot</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
  </head>
  <body class="be-splash-screen">
    <div class="be-wrapper be-login">
      <div class="be-content">
        <div class="main-content container-fluid">
          <div class="splash-container forgot-password">
            <div class="panel panel-default panel-border-color panel-border-color-primary">
              <div class="panel-heading"><img src="assets/img/tp.png" alt="logo" width="100" height="100" class="logo-img"><span class="splash-description">Forgot your password?</span></div>
              <div class="panel-body">
                <form name="myform" id="myform">
                <input type="hidden" name="section" value="send_pw">
                  <p>Don't worry, we'll send you an email to reset your password.</p>
                  <div class="form-group xs-pt-20">
                    <input type="email" name="email" required="" placeholder="Your Email" autocomplete="off" class="form-control">
                  </div>

		  <div class="form-group xs-pt-20">
			<input type="text" name="security" placeholder="What is <?=$_SESSION['rand1'];?> plus <?=$_SESSION['rand2'];?> ?">
		  </div>

                  <p class="xs-pt-5 xs-pb-20">Don't remember your email? <a href="mailto:admin@ticketpointe.com">Contact Support</a>.</p>
                  <div class="form-group xs-pt-5">
                    <button type="submit" class="btn btn-block btn-primary btn-xl">Reset Password</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="splash-footer"><a href="https://ticketpointe.com">Ticket Pointe Home</a></div>
            <div class="splash-footer">&copy; 2016 Ticket Pointe, LLC</div>
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
			?>
			<script>
			document.location.href='login?p=1';
			</script>
			<?php
			print "<font color=green>Your login details has been sent to your registered email address.</font><br>";
	
		}
	}

	if (($err1 == "1") or ($found != "1")) {
                $_SESSION['rand1'] = rand(5,10);
                $_SESSION['rand2'] = rand(5,10);
		?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/img/logo-fav.png">
    <title>Forgot</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
  </head>
  <body class="be-splash-screen">
    <div class="be-wrapper be-login">
      <div class="be-content">
        <div class="main-content container-fluid">
          <div class="splash-container forgot-password">
            <div class="panel panel-default panel-border-color panel-border-color-primary">
              <div class="panel-heading"><img src="assets/img/tp.png" alt="logo" width="100" height="100" class="logo-img"><span class="splash-description">Forgot your password?</span></div>
              <div class="panel-body">
                <form name="myform" id="myform">
                <input type="hidden" name="section" value="send_pw">
                  <p>Don't worry, we'll send you an email to reset your password.</p>
                  <div class="form-group xs-pt-20">
                    <input type="email" name="email" required="" placeholder="Your Email" autocomplete="off" class="form-control">
                  </div>

                  <div class="form-group xs-pt-20">
                        <input type="text" name="security" placeholder="What is <?=$_SESSION['rand1'];?> plus <?=$_SESSION['rand2'];?> ?">
                  </div>

                  <p class="xs-pt-5 xs-pb-20">Don't remember your email? <a href="mailto:admin@ticketpointe.com">Contact Support</a>.</p>
                  <div class="form-group xs-pt-5">
                    <button type="submit" class="btn btn-block btn-primary btn-xl">Reset Password</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="splash-footer"><a href="https://ticketpointe.com">Ticket Pointe Home</a></div>
            <div class="splash-footer">&copy; 2016 Ticket Pointe, LLC</div>
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
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/footer.phtml";
	$template->load_template($file,$null);
}
?>
