<?php
session_start();
require_once 'classes/Devotee.php';

function devCntctEntryWindow() {
	$clist = Centres::getList();
	$proglist = Programs::getList();

	$selCentre = -1;
	$selProgram = -1;
	
	if ( isset($_SESSION['Centre'])  ) {
		$selCentre = $_SESSION['Centre'];
	}
	
	echo '<form id="frmAddDevotee" enctype="multipart/form-data" action="modules/devotee/add_devotees.php" method="POST">';
	
	echo "<p>";
	echo "<SELECT id=\"Centre\" name='Centre' style='width:200px;'>";
	foreach( array_keys($clist) as $cname){
	
		$selected = '';
		if ( $selCentre == $clist[$cname] )  {
			$selected = 'selected' ;
		}
	
		echo "<option $selected value=" . $clist[$cname]  . ">"  .  $cname  .  "</option>";
	}
	
	echo "</SELECT>";
	echo "</p>";
	
	echo "<p>";
	echo "<SELECT id=\"Program\" name='Program' style='width:200px;'>";
	echo "<option>wait...</option>";
	echo "</SELECT>";
	echo "</p>";
	
	echo '<input type="hidden" name="frmtype" value="contact" />';
	echo "<p>";
	echo '*Contact: <INPUT TYPE="TEXT" Maxlength=10 Name="Contact" Id="Contact" value="">';
	echo "</p>";
	
	echo '<input type="submit" value="Next" />';
	echo "</p>";
	echo '</form>';
}

$frmtype = "contact";
if ( isset($_POST['frmtype'])  ) {
	$frmtype = $_POST['frmtype'];
}

if ( $frmtype == "contact" ) {
	devCntctEntryWindow();
	return;	
}





