<?php
include("/usr/local/cpanel/php/cpanel.php");  // Instantiate the CPANEL object.
$cpanel = new CPANEL();                       // Connect to cPanel - only do this once.
?>
 
<?php
$cpanel->end();                               // Disconnect from cPanel - only do this once.
?>
