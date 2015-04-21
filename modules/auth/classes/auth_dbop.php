<?php

require_once 'includes/constants.php';

class AuthDB {
	private $conn;

	function __construct() {
		$this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or
					  die('There was a problem connecting to the database.');
	}

	function verify_Username_and_Pass($un, $pwd) {

		$query = "SELECT *
				FROM users
				WHERE usr = ? AND usrpwd = ?
				LIMIT 1";

		if($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param('ss', $un, $pwd);
			$stmt->execute();

			if($stmt->fetch()) {
				$stmt->close();
				return true;
			} else {
				return false;
			}
		}

	}


	function getUserRoles( $user ) {
		$query = "SELECT role FROM usrroles where usr = ?";
		$retval = array();
		if($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param('s', $user);

			/* bind result variables */
			$stmt->bind_result($role);

			$stmt->execute();

			while ($stmt->fetch()) {
				$retval[] = $role;
			}
			$stmt->close();
		}

		return $retval;
	}


}


