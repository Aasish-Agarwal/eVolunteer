<?php
session_start();


function getProgramListItems () {
	$selProgram = -1;
	
	if ( isset($_SESSION['Program'])  ) {
		$selProgram = $_SESSION['Program'];
	}
	
	if ( ! isset($_SESSION['IsVald'] )  || $_SESSION['IsVald'] != 1 ) {
		return;
	}
	
	require_once 'classes/Devotee.php';
	DebugMessageProcessor::setDebugLevel(1000);

	$id = $_POST['id'];

	$associatedPrograms = CentrePrograms::getPrograms($id);
	$programsIdArray = $associatedPrograms->getList(); 	

	foreach( array_keys($programsIdArray) as $program_id){
		$selected = '';
		if ( $selProgram == $program_id )  {
			$selected = 'selected' ;
		}
		
		echo "<option $selected value=" . $program_id  . ">"  .  Programs::getName($program_id) .  "</option>";
	}
}

getProgramListItems ();
