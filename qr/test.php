<?php
include('QRGenerator.php');
$qrcode = new QRGenerator('Hello Robert',100);  // 100 is the qr size
print "<img src='". $qrcode->generate() ."'>"
?>
