<?php
session_start();
require_once 'classes/admin.php';
$admin = new Admin();

// If the user clicks the "Log Out" link on the index page.
if(isset($_GET['status']) && $_GET['status'] == 'authorized') {
	//$membership->log_User_Out();
}

echo $admin->get_roles();

?>

