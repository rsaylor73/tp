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

//$file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
$file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_header.phtml";
//$template->load_template($file,$null);
//$device = $tickets->device_type();
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
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/lib/material-design-icons/css/material-design-iconic-font.min.css"/><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    

    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
  </head>
  <body class="be-splash-screen">
    <div class="be-wrapper be-login be-signup">
      <div class="be-content">
        <div class="main-content container-fluid">
          <div class="splash-container sign-up">
            <div class="panel panel-default panel-border-color panel-border-color-primary">
              <div class="panel-heading"><img src="assets/img/tp.png" alt="logo" width="100" height="100" class="logo-img"><span class="splash-description">Please enter your user information.</span></div>
              <div class="panel-body">
		<div id="registration">
		<form name="myform">
                <span class="splash-title xs-pb-20">Sign Up</span>
                  <div class="form-group">
			<input type="text" name="uuname" id="uuname" placeholder="Username" onblur="check_uu(this.form)" class="form-control">
                  </div>
                  <div class="form-group">
			<input type="text" name="email" id="email" onblur="check_email(this.form)" placeholder="E-mail" class="form-control">
                  </div>
                  <div class="form-group row signup-password">
                    <div class="col-xs-6">
			<input type="password" name="pass1" id="pass1" placeholder="Password" class="form-control">
                    </div>
                    <div class="col-xs-6">
			<input type="password" name="pass2" id="pass2" onkeyup="check_pw(this.form)" placeholder="Confirm" class="form-control">
                    </div>
                  </div>
                  <div class="form-group xs-pt-10">
			<input type="button" class="btn btn-block btn-primary btn-xl" value="Sign Up" onclick="register(this.form)">

                  </div>
                  
                  <div class="form-group">
                      <label class="splash-title">Account Type</label>
                      <div class="col-xs-offset-3">
                        <div class="be-radio inline">
                          <input type="radio" checked="" name="account_type" value="1">
                          <label for="rad6">Personal</label>
                        </div>
                        <div class="be-radio inline">
                          <input type="radio" checked="" name="account_type" value="2">
                          <label for="rad7">Business</label>
                        </div>
                        </div>
                      
                    </div>

                  <div class="form-group xs-pt-10">
                    <div class="be-checkbox">
			<input type="checkbox" name="terms" value="checked" checked>
                      <label for="remember">By creating an account, you agree the <a href="https://www.ticketpointe.com/terms.html">terms and conditions</a>.</label>
                    </div>
                  </div>

                </form>
		</div>
              </div>
            </div>
            <div class="splash-footer"><a href="https://ticketpointe.com">Ticket Pointe Home</a></div>
            <div class="splash-footer"><span>Have an account? <a href="login">Sign In</a></span></div>
            <br>
            <div class="splash-footer">&copy; <?php $date = date("Y"); echo $date; ?> Ticket Pointe, LLC</div>

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

