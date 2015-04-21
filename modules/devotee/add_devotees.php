<?php
session_start();


function showDevoteeDetailsForm () {
	if ( ! isset($_SESSION['IsVald'])  || $_SESSION['IsVald'] != 1 ) {
		echo "<h1><span style=\"background-color:red\">This operation requires lon in to the system</span></h1><br>";
		return;
	}

	$_SESSION['Centre'] = $_POST['Centre'];
	$_SESSION['Program'] = $_POST['Program'];

	echo '<form id="frmDevoteeDetails" enctype="multipart/form-data" action="modules/devotee/add_devotees.php" method="POST">';
	
	echo '<input type="hidden" name="frmtype" value="contact" />';
	echo "<p>";
	echo '*Field 1: <INPUT TYPE="TEXT" Maxlength=10 Name="f1" Id="f1" value="">';
	echo "</p>";
	echo "<p>";
	echo '*Field 1: <INPUT TYPE="TEXT" Maxlength=10 Name="f2" Id="f2" value="">';
	echo "</p>";
	echo "<p>";
	echo '*Field 1: <INPUT TYPE="TEXT" Maxlength=10 Name="f3" Id="f3" value="">';
	echo "</p>";
	echo '<input type="hidden" name="frmtype" value="devdetails" />';
	
	echo '<input type="submit" value="Finish" />';
	echo "</p>";
	echo '</form>';
	
}

function procDevoteeDetailsForm () {
	if ( ! isset($_SESSION['IsVald'])  || $_SESSION['IsVald'] != 1 ) {
		echo "<h1><span style=\"background-color:red\">This operation requires lon in to the system</span></h1><br>";
		return;
	}
	
	echo "<hr>Got Details<hr>";
	var_dump($_POST);
	
}


if ( $_POST['frmtype'] ==  'contact' ) { 
	showDevoteeDetailsForm ();
}

if ( $_POST['frmtype'] ==  'devdetails' ) {
	procDevoteeDetailsForm  ();

}


