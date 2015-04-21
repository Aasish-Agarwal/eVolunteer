<?php
session_start();

require_once 'classes/auth.php';

function getAuthStatus () {
	$data = array();
	
	$data['callstatus'] = 'OK';
	$data['IsVald'] = 0;
	$data['User'] = "";

	if ( isset($_SESSION['IsVald'] )  ) {
		$data['IsVald'] = $_SESSION['IsVald'] ;
		$data['User'] = $_SESSION['User'];
	}
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function actionProcessor () {
	$action = "NULL";

	if ( isset($_POST['action'] )  ) {
		$action = $_POST['action'];
	}
	
	if ( isset($_GET['action'] )  ) {
		$action = $_GET['action'];
	}
	
	if ( $action == "NULL"  ) {
		$data = array();
		$data['callstatus'] = 'FAIL';
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($data);
		return;
	}

	if ( $action == "getAuthStatus" ) {
		getAuthStatus();
	}
}

actionProcessor ();

