<?php

require_once 'includes/constants.php';

class AdminDB {
	private $conn;

	function __construct() {
		$this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or
					  die('There was a problem connecting to the database.');

	}

	function get_roles() {
		$query = "SELECT role FROM roles";

		$json   = array();

		if($stmt = $this->conn->prepare($query)) {
			$stmt->execute();
		    $stmt->bind_result($role);

			while ($stmt->fetch()) {
				$json[] = $role;
			}
		} else {
				echo ' PREPARE FAILED';
		}

	    $stmt->close();

		header("Content-Type: text/json");
		echo json_encode(array( 'roles'  =>   $json ));
	}
}


