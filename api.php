<?php

// init
include_once "include/settings.php";
include_once "include/mysql.php";

$linkID = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (mysqli_connect_errno()) {
   printf("Connect failed: %s\n", mysqli_connect_error());
   exit();
}


class JS{
	public $linkID;

	function __construct($linkID){ $this->linkID = $linkID; }

	public function new_mysql($sql) {
		$result = $this->linkID->query($sql) or die($this->linkID->error.__LINE__);
		return $result;
	}

	public function return_js_error($msg) {
		// generate json error
		$data = array();
		$data['error'] = $msg;
		echo json_encode($data);
	}
}

$JS = new JS($linkID);

$_POST['json'] = stripslashes($_POST['json']);
header('Content-Type: application/json');

if (!empty($_POST['json'])) {
	$json_data = json_decode($_POST['json'], true);

	// check api credentials
	$api_username = $json_data['api']['user'];
	$api_password = $json_data['api']['pass'];

	$sql = "SELECT `api_username`,`api_password` FROM `settings` WHERE `id` = '1'";
	$result = $JS->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		if (($row['api_username'] == $api_username) && ($row['api_password'] == $api_password)) {
			$auth = "yes";
		}
	}

	if ($auth != "yes") {
		$JS->return_js_error('Unauthorized');
		die;
	}

	// check check in user credentials
	$checkin_user = $json_data['user']['checkin_email'];
	$checkin_pass = $json_data['user']['checkin_password'];

	$sql = "SELECT `resellerID`,`firstname`,`lastname` FROM `checkin_users` WHERE `email` = '$checkin_user' AND `password` = '$checkin_pass'";
	$result = $JS->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		$user_ok = "yes";
		$resellerID = $row['resellerID'];
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];	
	}

	if ($user_ok != "yes") {
		$JS->return_js_error('Incorrect login');
		die;
	}

	// what do we want to do
	$request = $json_data['request']['action'];

	switch ($request) {
		case "get_events":
			$sql = "
			SELECT
				`e`.`id`,
				`e`.`title`,
				`e`.`tagline`,
				`e`.`start_date`,
				`e`.`end_date`,
				`e`.`start_time`,
				`e`.`end_time`

			FROM
				`events` e

			WHERE
				`e`.`userID` = '$resellerID'

			ORDER BY `e`.`title` ASC

			";

			$data = array();
			$result = $JS->new_mysql($sql);
			while ($row = $result->fetch_assoc()) {
				// get total
				$sql2 = "SELECT COUNT(`id`) AS 'total' FROM `cart` WHERE `eventID` = '$row[id]' AND `status` = 'Paid'";
				$result2 = $JS->new_mysql($sql2);
				$row2 = $result2->fetch_assoc();

				// get total checked in
                                $sql3 = "SELECT COUNT(`id`) AS 'total' FROM `cart` WHERE `eventID` = '$row[id]' AND `status` = 'Paid' AND `consumed` = 'Yes'";
                                $result3 = $JS->new_mysql($sql3);
                                $row3 = $result3->fetch_assoc();

				$id = $row['id'];

				$data[$id]['id'] = $row['id'];
				$data[$id]['title'] = $row['title'];
				$data[$id]['start_date'] = $row['start_date'];
				$data[$id]['end_date'] = $row['end_date'];
				$data[$id]['start_time'] = $row['start_time'];
				$data[$id]['end_time'] = $row['end_time'];
				if ($row2['total'] == "") {
					$row2['total'] = "0";
				}
				$data[$id]['total'] = $row2['total'];
				if ($row3['total'] == "") {
					$row3['total'] = "0";
				}
				$data[$id]['checked_in'] = $row3['total'];
			}

			echo json_encode($data);
		break;
	}


} else {
	$JS->return_js_error('Incorrect query string');
}
?>
