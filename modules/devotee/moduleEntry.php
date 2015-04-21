<?php
session_start();

require_once 'classes/Devotee.php';
DebugMessageProcessor::setDebugLevel(1000);

function jsonTest () {

	$data = array();

	$data['callstatus'] = 'OK';

	$data['summary'] = array();
	$data['distribution'] = array();
	$data['Education'] = array();
	$data['Profession'] = array();


	$data['summary']['23 Jan'] = "20";
	$data['summary']['30 Jan'] = "52";
	$data['summary']['07 Feb'] = "83";

	$data['distribution'][] = "Education";
	$data['distribution'][] = "Profession";

	$data['Education']['Law'] = "90";
	$data['Education']['Accounts'] = "11";
	$data['Education']['Engineering'] = "24";

	$data['Profession']['Admin'] = "25";
	$data['Profession']['Business'] = "12";
	$data['Profession']['IT'] = "43";

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getCentreProgramMeta () {
	$data = array();

	$data['callstatus'] = 'OK';
	$data['centres'] = array ();
	$data['centre_programs'] = array ();
	
#	$data['centres']["C1"] = 101;
#	$data['centres']["C2"] = 102;
#	$data['centres']["C3"] = 103;
	
#	$prog_c1 = array();
#	$prog_c2 = array();
#	$prog_c3 = array();
	
#	$prog_c1["P1 C1"] = 1;
#	$prog_c1["P2 C1"] = 2;
#	$prog_c1["P3 C1"] = 3;
	
#	$prog_c2["P1 C2"] = 4;
#	$prog_c2["P2 C2"] = 5;
#	$prog_c2["P3 C2"] = 6;

#	$prog_c3["P1 C3"] = 7;
#	$prog_c3["P2 C3"] = 8;
#	$prog_c3["P3 C3"] = 9;
	
#	$data['centre_programs'][101] = $prog_c1;
#	$data['centre_programs'][102] = $prog_c2;
#	$data['centre_programs'][103] = $prog_c3;

	$clist = Centres::getList();
	foreach( array_keys($clist) as $cname){
		$cid = $clist[$cname];
		$data['centres'][$cname] = $cid;
	
		$prog = array();
		
		$associatedPrograms = CentrePrograms::getPrograms($cid);
		$programsIdArray = $associatedPrograms->getList();
		
		foreach( array_keys($programsIdArray) as $program_id){
			$prog[Programs::getName($program_id)] = $program_id;
		}
		$data['centre_programs'][$cid] = $prog;
	}
	
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getCentres () {
	$clist = Centres::getList();
	$data = array();
	
	$data['callstatus'] = 'OK';
	$data['centres'] = array ();
	
	foreach( array_keys($clist) as $cname){
		$data['centres'][$cname] = $clist[$cname];
	}

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getAllPrograms () {
	$plist = Programs::getList();
	$data = array();

	$data['callstatus'] = 'OK';
	$data['programs'] = array ();

	foreach( array_keys($plist) as $pname){
		$data['programs'][$pname] = $plist[$pname];
	}

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}


function getPrograms () {
	
	if ( ! isset($_POST['id'] )  ) {
		$data = array();
		$data['callstatus'] = 'FAIL';
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($data);
		return;
	}

	$centre_id = $_POST['id'];

	$associatedPrograms = CentrePrograms::getPrograms($centre_id);
	$programsIdArray = $associatedPrograms->getList();

	

	$data = array();
	$data['callstatus'] = 'OK';
	$data['id'] = $_POST['id'];
	$data['programs'] = array ();

	
	foreach( array_keys($programsIdArray) as $program_id){
		$data['programs'][Programs::getName($program_id)] = $program_id;
	}
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getAttendence() {
	
	$centre_id = $_POST['centre_id'];
	$prog_id = $_POST['prog_id'];
	$numEvents = $_POST['numEvents'];
	$period = $_POST['period'];
	
	$data = array();
	$data['callstatus'] = 'OK';
	$data['centre_id'] = $centre_id;
	$data['prog_id'] = $prog_id;
	$data['numEvents'] = $numEvents;
	$data['period'] = $period;
	$data['attendence'] = DevPMan::getAttendenceData($centre_id, $prog_id, $period);
	
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getAttendenceTrends() {
	
	$centre_id = $_POST['centre_id'];
	$prog_id = $_POST['prog_id'];
	$numEvents = $_POST['numEvents'];
	$period = $_POST['period'];
	
	$data = array();
	$data['callstatus'] = 'OK';
	$data['centre_id'] = $centre_id;
	$data['prog_id'] = $prog_id;
	$data['numEvents'] = $numEvents;
	$data['period'] = $period;
	$data['attendence'] = DevPMan::getAttendenceTrends($centre_id, $prog_id, $period);

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}


function getDistribution() {
	$centre_id = $_POST['centre_id'];
	$prog_id = $_POST['prog_id'];
	$attribute = $_POST['attribute'];
	$period = $_POST['period'];
	
	$data = array();
	$data['callstatus'] = 'OK';
	$data['centre_id'] = $centre_id;
	$data['prog_id'] = $prog_id;
	$data['attribute'] = $attribute;
	$data['period'] = $period;
	
	$data['unspecified'] = Devotee::getUnSpecified($centre_id, $prog_id, $attribute,$period); 
	$data['distribution'] = Devotee::getDistribution($centre_id, $prog_id, $attribute,$period); 
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getRegDevCntreProgAttrib() {
	$centre_id = $_POST['centre_id'];
	$prog_id = $_POST['prog_id'];
	$attribute = $_POST['attribute'];
	$attribval = $_POST['attribval'];
	$period = $_POST['period'];
	$fldlst = array("Id","Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender");

	$data = array();
	
	$data['callstatus'] = 'OK';
	$data['action'] = 'getRegDevCntreProgAttrib';
	$data['centre_id'] = $centre_id;
	$data['prog_id'] = $prog_id;
	$data['attribute'] = $attribute;
	$data['attribval'] = $attribval;
	$data['fldlst'] = $fldlst; 
	$data['period'] = $period; 
	
	$data['individuals'] = Devotee::getRegDevCntreProgAttrib($centre_id, $prog_id, $attribute,$attribval,$fldlst,$period);

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getEventDev () {
	$event_id = $_POST['event_id'];
	$fldlst = array("Id","Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender");
	
	$data = array();
	
	$data['callstatus'] = 'OK';
	$data['action'] = 'getEventDev';
	$data['event_id'] = $event_id;
	$data['fldlst'] = $fldlst;
	
	$data['individuals'] = Devotee::getEventDev($event_id,$fldlst);
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}


function getAbsenteeDev () {
	$centre_id = $_POST['centre_id'];
	$prog_id = $_POST['prog_id'];
	$event_date = $_POST['event_date'];
	$fldlst = array("Id","Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender");

	$data = array();

	$data['callstatus'] = 'OK';
	$data['action'] = 'getAbsenteeDev';
	$data['centre_id'] = $centre_id;
	$data['prog_id'] = $prog_id;
	$data['event_date'] = $event_date;
	$data['fldlst'] = $fldlst;

	$data['individuals'] = Devotee::getAbsenteeDev($centre_id,$prog_id,$event_date,$fldlst);

	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getLstTypeDevAttrib(){
	$data = array();
	$data['callstatus'] = 'OK';
	$data['attributes'] = DevAttributes::getListAttributes(); 
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);
}

function getDevExport() {

	header("Content-type: text/x-csv");
	//
	//Generate csv
	//

	if ( isset($_GET['category'] )  ) {
		$category = $_GET['category'];
		
		if ( $category == 'attribute' ) {
			$centre_id = $_GET['centre_id'];
			$prog_id = $_GET['prog_id'];
			$export_type = $_GET['export_type'];
			$period =  0;
		
			if ( isset($_GET['period'] )  ) {
				$period =  $_GET['period'];
			}
			
			$cname=Centres::getName($centre_id);
			$pname=Programs::getName($prog_id);
			
			$attribute = $_GET['attribute'];
			$attribval = $_GET['attribval'];
				
			header("Content-Disposition: attachment; filename=${cname}_${pname}_${attribute}.csv");

			$csvOutput = Devotee::expRegDevCntreProgAttrib($centre_id, $prog_id,$attribute,$attribval,$period);
			echo $csvOutput;
			return;
		}
	
		if ( $category == 'absentees' ) {
			$centre_id = $_GET['centre_id'];
			$prog_id = $_GET['prog_id'];
			$export_type = $_GET['export_type'];
			$period =  0;
		
			if ( isset($_GET['period'] )  ) {
				$period =  $_GET['period'];
			}
			
			$cname=Centres::getName($centre_id);
			$pname=Programs::getName($prog_id);
			
			header("Content-Disposition: attachment; filename=${cname}_${pname}_absentees.csv");
			$event_date = $_GET['event_date'];
			$csvOutput = Devotee::expAbsenteeDev($centre_id, $prog_id,$event_date);
			echo $csvOutput;
			return;
		}
		
		if ( $category == 'attendence' ) {
			header("Content-Disposition: attachment; filename=alldevotees.csv");
			$event_id = $_GET['event_id'];
			$csvOutput = Devotee::expEventDev($event_id);
			echo $csvOutput;
			return;
		}
		
		if ( $category == 'firsttimers' ) {
			header("Content-Disposition: attachment; filename=firsttimers.csv");
			$event_id = $_GET['event_id'];
			$csvOutput = Devotee::expEventDevNew($event_id);
			echo $csvOutput;
			return;
		}
		
		return;
	}
	
	$centre_id = $_GET['centre_id'];
	$prog_id = $_GET['prog_id'];
	$export_type = $_GET['export_type'];
	$period =  0;

	if ( isset($_GET['period'] )  ) {
		$period =  $_GET['period'];
	}
	
	$cname=Centres::getName($centre_id);
	$pname=Programs::getName($prog_id);
	
	header("Content-Disposition: attachment; filename=${cname}_${pname}_${export_type}.csv");
	$csvOutput = Devotee::exportCentreProgDev($centre_id, $prog_id,$period);
	echo $csvOutput;
	#exit();
}

function actionProcessor () {
	if ( ! isset($_SESSION['IsVald'] )  || $_SESSION['IsVald'] != 1 ) {
		return;
	}

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

	if ( $action == "getCentreProgramMeta" ) {
		getCentreProgramMeta();
	}
	
	
	if ( $action == "getCentres" ) {
		getCentres();
	}
	
	if ( $action == "getPrograms" ) {
		getPrograms();
	}

	if ( $action == "getAllPrograms" ) {
		getAllPrograms();
	}
	
	if ( $action == "getAttendence" ) {
		getAttendence();
	}
	
	if ( $action == "getDistribution" ) {
		getDistribution();
	}
	
	if ( $action == "getLstTypeDevAttrib" ) {
		getLstTypeDevAttrib();
	}
	
	if ( $action == "getRegDevCntreProgAttrib" ) {
		getRegDevCntreProgAttrib();
	}
	
	if ( $action == "getEventDev" ) {
		getEventDev();
	}

	if ( $action == "getAttendenceTrends" ) {
		getAttendenceTrends();
	}
	
	if ( $action == "getAbsenteeDev" ) {
		getAbsenteeDev();
	}

	if ( $action == "getDevExport" ) {
		getDevExport();
	}
	
}

actionProcessor ();

