<?php
session_start();
echo '<A href="../../operator.html">Back</A>';
echo '<hr>';

function isOpAllowed () {
	$retval = 0;
	if ( isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin'] ) {
		$retval = 1;
	}	
	if ( isset($_SESSION['dentry']) && $_SESSION['dentry'] ) {
		$retval = 1;
	}	
	return $retval;
}

function manageUploadedFile () {
	if ( ! isOpAllowed() ) {
		echo "<h1><span style=\"background-color:red\">Sorry but this feature is not available in your role</span></h1><br>";
		exit();
	}
	
	if ($_FILES["file"]["error"] > 0)
	{
		echo "<h1><span style=\"background-color:red\">There were errors in uploading file. Please ensure that you selected correct file</span></h1><br>";
		echo "<h3><span style=\"background-color:red\">Share the error code with administrator if the problem persists - Error Code: " . $_FILES["file"]["error"] . "</span></h3><br>";
		exit();
	}
	
	move_uploaded_file($_FILES["file"]["tmp_name"],"../../../upload/" . $_FILES["file"]["name"]);

	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
	
	date_default_timezone_set('Europe/London');
	
	/** Include PHPExcel_IOFactory */
	require_once '../../../Classes/PHPExcel/IOFactory.php';
	require_once 'classes/Devotee.php';
	
	$excelFile = "../../../upload/" . $_FILES["file"]["name"];
	
	DebugMessageProcessor::setDebugLevel(1000);
	
	
	if ( $_POST['ImportType'] ==  'Attendence' ) {
		DevoteeImporter::importAttendenceFromExcel($excelFile, $_POST['Centre'], $_POST['Program'],$_SESSION['User']);
	}

	if ( $_POST['ImportType'] ==  'Regisrtration' ) {
		DevoteeImporter::importFromExcel($excelFile , $_POST['Centre'],$_SESSION['User']);
	}
	
	if ( $_POST['ImportType'] ==  'Merge' ) {
		DevoteeImporter::merge($excelFile, $_POST['Centre'], $_POST['Program'],$_SESSION['User']);
	}
	
	echo '<p></p><hr>';
	
	echo '<A href="../../operator.html">Back</A>';
	echo '<p></p><hr>';
	echo "Upload: " . $_FILES["file"]["name"] . "<br />";
	echo "Type: " . $_FILES["file"]["type"] . "<br />";
	echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
}

manageUploadedFile ();
