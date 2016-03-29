<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";

if ($_SERVER['HTTP_HOST'] != $GLOBAL['domain']) {
        $sub = explode(".",$_SERVER['HTTP_HOST']);

	$settings = $tickets->get_settings();
	if ($settings[9] == "Off") {
		$http = "http://";
	}
	if ($settings[9] == "On") {
		$http = "https://";
	}

	if ($sub[0] != "ticketpointe") {
	        $redirect = $http.$GLOBAL['domain']."/".$sub[0];
		print "<br><br><br>
		<br><br><br>
                <br><br><br>
                <br><br><br>
                <br><br><br>
                <br><br><br>

		<center><h2>Loading...</h2></center><br><br><bR>";
		$_GET['h'] = "no";
		print "<meta http-equiv=\"refresh\" content=\"1;url=$redirect\">";
        	//header("Location: $redirect");
	}
}

// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}

$today = date("Ymd");
$sql2 = "SELECT DISTINCT `title` FROM `events` WHERE DATE_FORMAT(`end_date`, '%Y%m%d') >= '$today' ";
$result2 = $tickets->new_mysql($sql2);

while ($row2 = $result2->fetch_assoc()) {
      
      $data22 .= '"';
      $data22 .= $row2['title'];
      $data22 .= '",';
}
$data22 = substr($data22,0,-1);


if ($_GET['h'] != "no") {
	if ($_POST['section'] == "cart") {
		$file = $GLOBAL['path'] . "/templates/" . $dir . "/header_cart.phtml";
                $template->load_template($file,$data22);
	} else {
		if ($_GET['section'] != "") {
			$section = $_GET['section'];
		}
		if ($_POST['section'] != "") {
			$section = $_POST['section'];
		}
		switch ($section) {
			case "dashboard":
			case "profile":
			case "create_checkin_user":
			case "signout":
			case "save_new_user":
			case "save_update_user":
			case "save_details":
			case "update_design":
			case "update_settings":
			case "manage_tickets":
			case "save_registration_form";
			case "update_details":
		                $file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_header.phtml";
                		$template->load_template($file,$data22);
			break;

			default:
						if ($_GET['section'] != "page_view") {
		                $file = $GLOBAL['path']  . "/templates/" . $dir . "/header.phtml";
        		        $template->load_template($file,$data22);
        		    	}
			break;
		}
	}
}


if (($_GET['section'] == "") && ($_POST['section'] == "")) {
	// get events

	$today = date("Y-m-d");
	// events
	$sql = "SELECT `title`,`id` FROM `events` WHERE '$today' BETWEEN `start_date` AND `end_date`";
	$result = $tickets->new_mysql($sql);
	while ($row = $result->fetch_assoc()) {
		$events .= "<option value=\"$row[id]\">$row[title]</option>"; 
	}
	if ($events == "") {
		$events = "<option value=\"\">There are no events</option>";
	}

	// location
	$sql = "SELECT * FROM `location` ORDER BY `location` ASC";
        $result = $tickets->new_mysql($sql);
        while ($row = $result->fetch_assoc()) {
		$location .= "<option value=\"$row[id]\">$row[location]</option>";
	}
	if ($location == "") {
		$location = "<option value=\"\">No locations</option>";
	}

	$data[] = $events;
	$data[] = $location;
	$data[] = $tickets->upcoming_events();
	
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/home.phtml";
	$template->load_template($file,$data);
}

if ($_GET['section'] == "search") {
	$result = $tickets->search();
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/search_results.phtml";
	$template->load_template($file,$result);

}


if ($_GET['section'] == "dashboard") {
	$is_logged = $tickets->check_login();
	if ($is_logged == "FALSE") {
		print "
	<script>
        setTimeout(function()
        {
	        window.location.replace('login')
        }
        ,0);

	</script>

		";

		die;
	}
	print "Test: $is_logged<br>";
        //$file = $GLOBAL['path']  . "/templates/" . $dir . "/dashboard.phtml";
        //$template->load_template($file,$null);

        $file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_nav.phtml";
        $template->load_template($file,$null);

        $file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_body.phtml";
        $template->load_template($file,$null);

}

if ($_POST['section'] == "save_details") {
	$tickets->save_details();
}
if ($_POST['section'] == "update_details") {
	$tickets->update_details();
}
if ($_POST['section'] == "update_design") {
	$tickets->update_design();
}
if ($_POST['section'] == "update_settings") {
	$tickets->update_settings();
}

if ($_POST['section'] == "save_new_user") {
	$tickets->save_new_user();
}
if ($_POST['section'] == "save_update_user") {
	$tickets->save_update_user();
}
if ($_POST['section'] == "save_registration_form") {
	$tickets->save_registration_form();
}
if ($_POST['section'] == "cart") {
	$tickets->cart();
}
if ($_POST['section'] == "donate") {
	$tickets->donate();
}
if ($_GET['section'] == "cancel") {
	$tickets->cancel();
}

if ($_POST['section'] == "checkout") {
	$tickets->cart_checkout();
}
if ($_POST['section'] == "payment") {
	$tickets->payment();
}

if ($_POST['section'] == "create_checkin_user") {
	$tickets->create_checkin_user();
}

if ($_POST['section'] == "free") {
	$tickets->free();
}

/*
if ($_GET['section'] == "qr_test") {
	$sesID = "2e2eda5ac601f2d5beb0e69fc9959507";
	$data['email'] = "robert@customphpdesign.com";
	$data['viewID'] = "345";
	$html = $tickets->qr_code($sesID,$data);

	print "$html";
	
}
*/

if ($_GET['section'] == "view_page") {
	$tickets->view_page();
}

if ($_GET['section'] == "signout") {
	$tickets->signoff();
}

if ($_GET['section'] == "page_view") {
	if ($dir == "desktop") {
		$tickets->page_view();
	} else {
		$tickets->page_view_mobile();
	}
}

if ($_POST['section'] == "profile") {
	$tickets->update_profile();
}

if ($_POST['section'] == "cart") {
	$file = $GLOBAL['path']  . "/templates/" . $dir . "/footer_cart.phtml";
	$template->load_template($file,$null);
} else {
                switch ($section) {
                        case "dashboard":
			case "profile":
			case "create_checkin_user":
			case "signout":
			case "save_new_user":
			case "save_update_user":
			case "save_details":
			case "update_design":
			case "update_settings":
			case "manage_tickets":
			case "save_registration_form";
			case "update_details":
                                $file = $GLOBAL['path']  . "/templates/" . $dir . "/hq_footer.phtml";
                                $template->load_template($file,$data22);
                        break;

			default:
					if ($_GET['section'] != "page_view") {
			        $file = $GLOBAL['path']  . "/templates/" . $dir . "/footer.phtml";
			        $template->load_template($file,$null);
			    	}
			break;
		}
}
?>
