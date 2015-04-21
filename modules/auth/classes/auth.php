<?php

require 'auth_dbop.php';

class Membership {

	function validate_user($un, $pwd) {
		$mysql = New AuthDB();
		$ensure_credentials = $mysql->verify_Username_and_Pass($un, md5($pwd));

		if($ensure_credentials) {
			$_SESSION['status'] = 'authorized';
			$ret_array = array('username'=>$un,'status'=>'OK');

			$_SESSION['User'] = $un;
			$_SESSION['IsVald'] = 1;

			$userroles = $mysql->getUserRoles($un);
			$this->set_usr_roles($userroles);
			echo json_encode($ret_array);
		} else {
			$ret_array = array('username'=>$un,'status'=>'FAIL');

			unset($_SESSION['IsAdmin']);
			unset($_SESSION['siteadmin']);
			unset($_SESSION['IsRepViewer']);
			unset($_SESSION['User']);
			unset($_SESSION['IsVald']);
			echo json_encode($ret_array);
		}
	}

	function log_User_Out() {
		if(isset($_SESSION['status'])) {
			unset($_SESSION['status']);
			if(isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time() - 1000);
				session_destroy();
		}
		$ret_array = array('status'=>'LOGGEDOUT');
		echo json_encode($ret_array);

	}

	function set_usr_roles($userroles) {
		foreach ($userroles as $role) {
				if ( $role == 'admin' ) {
					$_SESSION['IsAdmin'] = 1;
				}

				if ( $role == 'siteadmin' ) {
					$_SESSION['siteadmin'] = 1;
				}

				if ( $role == 'report' ) {
					$_SESSION['IsRepViewer'] = 1;
				}
		}
	}


	function confirm_Member() {
		session_start();
		if($_SESSION['status'] !='authorized') header("location: ../autneticate.php");
	}

}

