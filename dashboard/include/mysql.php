<?php

$DB_NAME = 'dub_tickets';
$DB_HOST = 'localhost';
$DB_USER = 'dub_tickets';
$DB_PASS = '^[nH$lt$~W}6';

$linkID = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (mysqli_connect_errno()) {
   printf("Connect failed: %s\n", mysqli_connect_error());
   exit();
}
?>
