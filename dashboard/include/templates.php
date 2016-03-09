<?php
/* -----------------------------------------
// This file controls the actions of the template class
// Version 1.00
// Author: Robert Saylor
// robert@customphpdesign.com
// Jan 31, 2015
*/

include $GLOBAL['path']."/class/templates.class.php";
$template = new Templates($linkID);

include $GLOBAL['path']."/class/tickets.class.php";
$tickets = new Tickets($linkID);

include $GLOBAL['path']."/class/admin.class.php";
$admin = new Admin($linkID);

include $GLOBAL['path']."/class/gwapi.class.php";
$gw = new gwapi;

if ($_GET['section'] == "logout") {
        $template->logout();
}

?>
