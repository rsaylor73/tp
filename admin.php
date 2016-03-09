<?php
session_start();

$sesID = session_id();

// init
include_once "include/settings.php";
include_once "include/mysql.php";
include_once "include/templates.php";


// Do desktop or mobile
$type = $template->isMobile();
if ($type) {
        $dir = "mobile";
} else {
        $dir = "desktop";
}

$file = $GLOBAL['path']  . "/templates/" . $dir . "/admin_header.phtml";
$template->load_template($file,$null);


$file = $GLOBAL['path']  . "/templates/" . $dir . "/admin_top.phtml";
$template->load_template($file,$null);


$check = $admin->check_login();
if ($check == "FALSE") {
	$admin->login($null);
} else {

	if (($_GET['section'] == "") && ($_POST['section'] == "")) {
		$admin->dashboard();
	}
	if ($_GET['section'] == "balance_report") {
		$admin->balance_report();
	}
	if ($_GET['section'] == "balance_report_donations") {
                $admin->balance_report('donations');
	}
	if ($_GET['section'] == "payout") {
		$admin->payout($_GET['type']);
	}
	if ($_GET['section'] == "mark_as_paid") {
		$admin->mark_as_paid($_GET['type']);
	}
	if ($_GET['section'] == "refund") {
		$admin->refund();
	}
	if ($_POST['section'] == "search") {
		$admin->search();
	}
	if ($_GET['section'] == "process_refund") {
		$admin->process_refund();
	}
	if ($_GET['section'] == "pages") {
		$admin->page();
	}
	if ($_POST['section'] == "update_page") {
		$admin->update_page();
	}
	if ($_GET['section'] == "users") {
		$admin->users();
	}
	if ($_GET['section'] == "edit_user") {
		$admin->edit_user();
	}
	if ($_POST['section'] == "update_user") {
		$admin->update_user();
	}
	if ($_GET['section'] == "new_user") {
		$admin->new_user();
	}
	if ($_POST['section'] == "save_user") {
		$admin->save_user();
	}
	// Account Type
	if ($_GET['section'] == "account_types") {
		$admin->account_types();
	}
	if ($_GET['section'] == "edit_type") {
		$admin->edit_type();
	}
	if ($_POST['section'] == "update_type") {
		$admin->update_type();
	}
	if ($_GET['section'] == "delete_type") {
		$admin->delete_type();
	}
	if ($_GET['section'] == "new_type") {
		$admin->new_type();
	}
	if ($_POST['section'] == "save_type") {
		$admin->save_type();
	}
	// Categories
	if ($_GET['section'] == "categories") {
		$admin->categories();
	}
        if ($_GET['section'] == "edit_category") {
                $admin->edit_category();
        }
        if ($_POST['section'] == "update_category") {
                $admin->update_category();
        }
        if ($_GET['section'] == "delete_category") {
                $admin->delete_category();
        }
        if ($_GET['section'] == "new_category") {
                $admin->new_category();
        }
        if ($_POST['section'] == "save_category") {
                $admin->save_category();
        }
	// Locations
	if ($_GET['section'] == "locations") {
		$admin->locations();
	}
        if ($_GET['section'] == "edit_location") {
                $admin->edit_location();
        }
        if ($_POST['section'] == "update_location") {
                $admin->update_location();
        }
        if ($_GET['section'] == "delete_location") {
                $admin->delete_location();
        }
        if ($_GET['section'] == "new_location") {
                $admin->new_location();
        }
        if ($_POST['section'] == "save_location") {
                $admin->save_location();
        }

	// Admin users
	if ($_GET['section'] == "admin_users") {
		$admin->admin_users();
	}
	if ($_GET['section'] == "edit_admin_user") {
		$admin->edit_admin_user();
	}
	if ($_POST['section'] == "update_admin_user") {
		$admin->update_admin_user();
	}
	if ($_GET['section'] == "new_admin_user") {
		$admin->new_admin_user();
	}
	if ($_POST['section'] == "save_admin_user") {
		$admin->save_admin_user();
	}
	if ($_GET['section'] == "delete_admin_user") {
		$admin->delete_admin_user();
	}
}


$file = $GLOBAL['path']  . "/templates/" . $dir . "/admin_bot.phtml";
$template->load_template($file,$null);

?>
