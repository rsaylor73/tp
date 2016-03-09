<?php

/*
Notes:
Allow unregistered domains [?] should be set to On in WHM

result returns this: JSON

{"apiversion":"1","type":"event","module":"Park","func":"park","source":"module","data":{"result":"robjoe2.robertsaylor.com was successfully parked on top of "},"event":{"result":1}}

parse the JSON to read the result.

*/

	function objectToArray($d) {
	if (is_object($d)) {
	// Gets the properties of the given object
	// with get_object_vars function
	$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
	/*
	* Return array converted to object
	* Using __FUNCTION__ (Magic constant)
	* for recursive call
	*/
	return array_map(__FUNCTION__, $d);
	}
	else {
	// Return array
	return $d;
	}
	}


                include_once 'class/xmlapi.php';
		$server_ip = "66.71.252.139";
                $xmlapi = new xmlapi($server_ip);
		//$server_user = "root";
		//$server_pw = "g67rb32x";
                //$xmlapi->password_auth($server_user,$server_pw);

		$domain_user = "robsay";
		$domain_pw = "c9brobra";


                # switch to cPanel
                $xmlapi->set_debug(1);
                $xmlapi->set_output('json');
                $xmlapi->set_port(2083);
                $xmlapi->password_auth($domain_user,$domain_pw);

                //$result = $xmlapi->api1_query( $domain_user, 'Mysql', 'adddb', array('test'));
                $result = $xmlapi->api1_query( $domain_user, 'Park', 'park', array('tt32wewewobjoe3.robertsaylor.com'));
		$json_data = json_decode($result);

                //print_r($json_data);

		$new_array = objectToArray($json_data);

		//print_r($new_array);

		$new_result = $new_array['data']['result'];
		if (preg_match('/was successfully/i', $new_result)) {
			print "The domain was created.\n";
		} else {
			print "The domain failed.\n";
		}

		//print_r($result);

		//$result = $xmlapi->api2_query( $domain_user, 'Park', 'park', array('robertsaylor.com','joetheman'));
                //$result = $xmlapi->api2_query( $domain_user, 'Park', 'park', array('joetestf.robertsaylor.com','joetestf.robertsaylor.com'));

                //print_r($result);

?>
