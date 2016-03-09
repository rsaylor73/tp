<?php

$GLOBAL['path'] = "/home/dub/www";
$GLOBAL['domain'] = "ticketpointe.customphpdesign.com";

// email headers - This is fine tuned, please do not modify
$sitename = "Ticket Pointe";
$site_email = "ticketpointe@ticketpointe.com";

$header = "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
$header .= "From: $sitename <$site_email>\r\n";
$header .= "Reply-To: $sitename <$site_email>\r\n";
$header .= "X-Priority: 3\r\n";
$header .= "X-Mailer: PHP/ 5.9.4.1\r\n";

$GLOBAL['header'] = $header;
?>
