<?php

require 'admin_dbop.php';

class Admin {

	function get_roles () {
		$mysql = New AdminDB();
		$rolelist = $mysql->get_roles();
		return $rolelist;
	}


}

