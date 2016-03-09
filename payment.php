<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

                print "<div id=\"page_view\">";

                $settings = $tickets->get_settings();
print "<pre>";
print_r($settings);
print "</pre>";

                require_once("2checkout/lib/Twocheckout.php");

                Twocheckout::privateKey($settings[11]); //Private Key
                Twocheckout::sellerId($settings[9]); // 2Checkout Account Number
                Twocheckout::sandbox($settings[12]); // Set to false for production accounts.

                try {
                    $charge = Twocheckout_Charge::auth(array(
                        "merchantOrderId" => "123",
                        "token"      => $_POST['token'],
                        "currency"   => 'USD',
                        "total"      => '10.00',
                        "billingAddr" => array(
                            "name" => 'Testing Tester',
                            "addrLine1" => '123 Test St',
                            "city" => 'Columbus',
                            "state" => 'OH',
                            "zipCode" => '43123',
                            "country" => 'USA',
                            "email" => 'example@2co.com',
                            "phoneNumber" => '555-555-5555'
                        )
                    ));

                    if ($charge['response']['responseCode'] == 'APPROVED') {
                        echo "Thanks for your Order!";
                        echo "<h3>Return Parameters:</h3>";
                        echo "<pre>";
                        print_r($charge);
                        echo "</pre>";

                    }
                } catch (Twocheckout_Error $e) {
                    print_r($e->getMessage());
                }

                print "</div>";
?>
