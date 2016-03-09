<?php



$DB_NAME = 'db';

$DB_HOST = 'localhost';

$DB_USER = 'user';

$DB_PASS = 'password';





$linkID = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);



if (mysqli_connect_errno()) {

   printf("Connect failed: %s\n", mysqli_connect_error());

   exit();

}

?>
