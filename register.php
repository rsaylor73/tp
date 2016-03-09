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

?>
<div id="page_view" style="text-align:center">
<h2>Register</h2>
                        <div id="registration">
                        <form name="myform">
                                <div class="modal-body">
                                <table width=500 style="text-align:center;" class="table">
                                <tr>
                                <td>Username: <div id="checkuu" style="display:inline"></div></td>
                                <td>Email: <div id="checkemail" style="display:inline"></div></td>
                                </tr>
                                <tr>
                                <td><input type="text" name="uuname" id="uuname" size=30 onblur="check_uu(this.form)"></td>
                                <td><input type="text" name="email" id="email" onblur="check_email(this.form)" size=30></td>
                                </tr>
                                <tr>
                                <td>Password:</td>
                                <td>Confirm Password: <div id="checkpw" style="display:inline"></div></td>
                                </tr>
                                <tr>
                                <td><input type="password" name="pass1" id="pass1" size=30></td>
                                <td><input type="password" name="pass2" id="pass2" onkeyup="check_pw(this.form)" size=30></td>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM `account_types` ORDER BY `description`";
                                $result2 = $tickets->new_mysql($sql);
                                while ($row2 = $result2->fetch_assoc()) {
                                        $options .= "<option value=\"$row2[id]\">$row2[description]</option>";
                                }
                                ?>
                                <tr><td colspan=2 align="center">Account Type</td></tr>
                                <tr><td colspan=2 align="center"><select name="account_type"><?=$options;?></select></td></tr>
                                <tr><td colspan=2 align="center"><input type="checkbox" name="terms" value="checked"> I agree to the <a href="terms.html" target=_blank>Terms & Conditions</a></td></tr>
                                <tr>
                                <td align="right">
                                <div class="g-recaptcha" data-sitekey="6LcnkA4TAAAAAD04ISkLGW_X0GWQnkbAUflFkI3E"></div>
                                </td>
                                <td align="left"><input type="button" class="btn btn-primary btn-lg" value="Register" onclick="register(this.form)"></td>
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
