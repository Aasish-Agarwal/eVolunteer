<?php
/**
 *	Devotee
 *
 */


require_once '../../includes/constants.php';
date_default_timezone_set('Europe/London');


class DBConnection
{
	private static $_conn;

	public static function getConnection() {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}
		return self::$_conn;
	}

	private static function init () {
		if (   isset(self::$_conn) ) {
			return;
		}
		self::$_conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or
		die('There was a problem connecting to the database.');
	}
}


/**
 *	Centres
 *
 *	@category   Centre Object
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class Centres
{
	private static $_name2id ;
	private static $_id2name ;
	private static $conn;

	public static function getList() {
		if (  ! isset(self::$_name2id) ) {
			self::init();
		}
		return self::$_name2id;
	}

	public static function getName($id) {
		if (  ! isset(self::$_id2name) ) {
			self::init();
		}
		return self::$_id2name[$id];
	}

	public static function getId($name) {
		if (  ! isset(self::$_name2id) ) {
			self::init();
		}

		if ( ! isset(self::$_name2id[$name])) {
			throw new Exception("Centres: Invalid $name");
		}
		return self::$_name2id[$name];
	}

	private static function init () {
		self::$conn = DBConnection::getConnection();

		$query = "SELECT id,name from _centre order by name";
		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($id,$name);

			/* fetch values */
			while ($stmt->fetch()) {
				#echo "<BR>$CentreName = $Id";
				self::$_name2id[$name] = $id;
				self::$_id2name[$id] = $name;
			}

			/* close statement */
			$stmt->close();
		}
	}
}


/**
 *	Programs
 *
 *	@category   Program Object
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class Programs
{
	private static $_name2id ;
	private static $_id2name ;
	private static $conn;

	public static function getList() {
		if (  ! isset(self::$_name2id) ) {
			self::init();
		}
		return self::$_name2id;
	}

	public static function getName($id) {
		if (  ! isset(self::$_id2name) ) {
			self::init();
		}
		return self::$_id2name[$id];
	}

	public static function getId($name) {
		if (  ! isset(self::$_name2id) ) {
			self::init();
		}

		if ( ! isset(self::$_name2id[$name])) {
			throw new Exception("Programs: Invalid $name");
		}
		return self::$_name2id[$name];
	}

	private static function init () {
		self::$conn = DBConnection::getConnection();

		$query = "SELECT id,name from _program order by name";
		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($id,$name);

			/* fetch values */
			while ($stmt->fetch()) {
				#echo "<BR>$CentreName = $Id";
				self::$_name2id[$name] = $id;
				self::$_id2name[$id] = $name;
			}

			/* close statement */
			$stmt->close();
		}
	}
}

/**
 *	DevAttributes
 *
 *	@category   Devotee Attributes
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class DevAttributes
{
	private static $_conn;


	private static function init () {
		if (  ! isset(self::$_conn) ) {
			self::$_conn = DBConnection::getConnection();
		}
	}

	public static function getListAttributes () {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = "SELECT property,dbname FROM attribute where proptype='list'";

		$retval = array();

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($property, $dbname);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval[] = array('property' => $property, 'dbname' => $dbname);
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}
}
/**
 *	DevPMan
 *
 *	@category   Devotee Participation Data Manager
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class DevPMan
{
	private static $_valList ;
	private static $_devotee ;
	private static $_conn;
	private static $_dataErrors;

	public static function getDataErrors() {
		return self::$_dataErrors;
	}

	public static function registerEvent($centre_id,$prog_id,$event_date) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		if ( self::getEventId($centre_id,$prog_id,$event_date) > 0 )
		{
			return ;
		}

		$query = "INSERT INTO _event (centre_id, prog_id, event_date) VALUES ($centre_id,$prog_id,$event_date)";
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}
	}

	public static function getEventId($centre_id,$prog_id,$event_date) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query = "select id from _event where centre_id=$centre_id AND prog_id=$prog_id AND event_date=$event_date";
		$retval = -1;

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($eventid);

			/* fetch values */
			while ($stmt->fetch()) {
				#echo "<BR>$CentreName = $Id";
				$retval = $eventid;
			}

			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	private static function getAbsenteeCount($centre_id, $prog_id, $event_date) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = " select distinct count(*) from _event_attendence A,_event E ";
		$query  .= " where A.event_id = E.id ";
		$query  .= " AND E.centre_id=$centre_id and E.prog_id=$prog_id ";
		$query  .= " AND E.event_date = $event_date ";
		$query  .= " AND A.devotee_id not in ( ";
		$query  .= " select distinct B.devotee_id from _event_attendence B,_event E1 ";
		$query  .= " where B.event_id = E1.id ";
		$query  .= " AND E1.centre_id=$centre_id and prog_id=$prog_id ";
		$query  .= " AND E1.event_date > $event_date) ";

		$retval = 0;
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($count);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval = $count;
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}


	/**
	 *	getEventList
	 *  Returns the list of event ordered by increasing event date for given centre and program
	 *	@centre_id  Centre Id
	 *	@prog_id	Program Id
	 */
	public static function getEventList($centre_id, $prog_id, $period) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		# Get the event list
		$query  = " select id,event_date from _event ";
		$query  .= " where centre_id=$centre_id and prog_id=$prog_id ";
		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query .= " AND event_date >= $event_date ";
		}
		$query  .= " order by event_date desc ";

		$retval = array();
		$evcount = 0;
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($id,$event_date);

			/* fetch values */
			while ($stmt->fetch()) {
				$evcount++;
				if ( $evcount > 0 ) {
					$retval[$id] = $event_date;
				}
			}
			/* close statement */
			$stmt->close();
		}
		return $retval ;
	}

	public static function getTotalAttendence($centre_id, $prog_id , $period) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = " SELECT A.devotee_id, count(A.devotee_id) FROM _event_attendence A , _event E ";
		$query .= " Where A.event_id = E.id ";
		$query .= " AND E.centre_id = $centre_id ";
		$query .= " AND E.prog_id = $prog_id ";

		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query .= " AND E.event_date >= $event_date ";
		}

		$query .= " group by A.devotee_id ";

		$retval = array();

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devotee_id, $presense);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval[$devotee_id] = $presense;
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	/**
	 *	getEventList
	 *  Returns the list of events that a devotee atended for given centre and program
	 *	@centre_id  Centre Id
	 *	@prog_id	Program Id
	 */
	public static function getDevEvents($devotee_id,$centre_id, $prog_id) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = " SELECT A.event_id FROM _event_attendence A , _event E ";
		$query .= " Where A.event_id = E.id ";
		$query .= " AND E.centre_id = $centre_id ";
		$query .= " AND E.prog_id = $prog_id ";
		$query .= " AND A.devotee_id = $devotee_id";
		$retval = array();

		$evcount = 0;
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($event_id);

			/* fetch values */
			while ($stmt->fetch()) {
				$evcount++;
				$retval[$event_id] = $devotee_id;
			}
			/* close statement */
			$stmt->close();
		}
		#return "Found Events : $evcount :$query:";
		return $retval;
	}


	public static function getAttendenceTrends($centre_id, $prog_id, $period) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		# Get the event list
		$query  = " select id,event_date from _event ";
		$query  .= " where centre_id=$centre_id and prog_id=$prog_id ";
		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query .= " AND event_date >= $event_date ";
		}
		$query  .= " order by event_date desc ";

		$eventdates = array();
		$evcount = 0;
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($id,$event_date);

			/* fetch values */
			while ($stmt->fetch()) {
				$evcount++;
				if ( $evcount > 1 ) {
					$eventdates[] = $event_date;
				}
			}
			/* close statement */
			$stmt->close();
		}

		# For each event - Get the devotees which are not coming
		$retval = array();
		foreach( $eventdates as $event_date ) {
			$absentees = self::getAbsenteeCount($centre_id, $prog_id, $event_date);
			$event_date_pr = date('d M Y', $event_date);
			$retval[] = array('event_date' => $event_date, 'absentees' => $absentees , 'Date' => $event_date_pr);
		}

		return $retval;
	}


	public static function getAttendenceData($centre_id, $prog_id, $period) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}


		$query  = " SELECT A.event_id, count(A.event_id),E.event_date  FROM _event_attendence A , _event E ";
		$query .= " Where A.event_id = E.id ";
		$query .= " AND E.centre_id = $centre_id ";
		$query .= " AND E.prog_id = $prog_id ";

		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query .= " AND E.event_date >= $event_date ";
		}

		$query .= " group by A.event_id ";
		$query .= " order by E.event_date desc ";



		$retval = array();

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($event_id, $presense, $event_date);

			/* fetch values */
			while ($stmt->fetch()) {
				$event_date = date('d M Y', $event_date);
				$retval[] = array('event_id' => $event_id , 'Date' => $event_date, 'Count' => $presense);

			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function assocProgram($devotee_id,$centre_id, $prog_id) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query = "INSERT INTO _program_enrollment (centre_id, prog_id, devotee_id) VALUES ($centre_id, $prog_id,$devotee_id)";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}
		return true;
	}
	public static function getTotalEventCount($centre, $program) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = " SELECT count(*) from _event ";
		$query  .= " WHERE centre_id=$centre and prog_id=$program  ";

		$evcount = 0;
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($evcount);

			/* fetch values */
			while ($stmt->fetch()) {
			}
			/* close statement */
			$stmt->close();
		}
		return $evcount;
	}

	public static function getAttendenceOffset($centre, $program) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		$query  = " SELECT d.`devotee_id`, d.`offset` FROM _dev_prog_offset d ";
		$query  .= " WHERE d.`centre_id`=$centre AND d.`prog_id`=$program ";

		$retval = array();

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devotee_id, $offset);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval[$devotee_id] = $offset;
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function setAttendenceOffset($devId,$centre, $program) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}

		# Get Events after the first event attended by devotee
		# Set offset
		$event_date = 0;
		$offset=0;

		$query  = " SELECT min(event_date) from _event ";
		$query .= " where centre_id=$centre and prog_id=$program ";
		$query .= " and id in ( ";
		$query .= " SELECT e.`event_id`FROM _event_attendence e ";
		$query .= " where e.`devotee_id`=$devId ";
		$query .= " ) ";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($event_date);

			/* fetch values */
			while ($stmt->fetch()) {
			}
			/* close statement */
			$stmt->close();
		}


		$query  = " SELECT count(*) from _event ";
		$query  .= " WHERE centre_id=$centre and prog_id=$program  ";
		$query  .= " AND event_date<$event_date ";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($offset);

			/* fetch values */
			while ($stmt->fetch()) {
			}
			/* close statement */
			$stmt->close();
		}

		$query  = " INSERT INTO _dev_prog_offset ";
		$query  .= " (centre_id,prog_id,devotee_id,offset) VALUES  ";
		$query  .= " ($centre,$program,$devId,$offset) ";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* close statement */
			$stmt->close();
		}
		return $offset;
	}


	public static function logAttendence($devId,$eventId,$centre, $program,$IgnoreSameContact) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}
		self::$_dataErrors = "";

		self::assocProgram($devId, $centre, $program);
		$query = "INSERT INTO _event_attendence (event_id, devotee_id) VALUES ($eventId,$devId)";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}
		
		Devotee::enable($devId);
		return true;
	}

	public static function Merge($devIdToRemove,$devIdTarget) {
		if (  ! isset(self::$_conn) ) {
			self::init();
		}
		self::$_dataErrors = "";


		$query  = "select distinct A.event_id from _event_attendence A ";
		$query .= "where A.devotee_id = $devIdToRemove ";
		$query .= "and A.event_id ";
		$query .= "not in (select event_id from _event_attendence where devotee_id = $devIdTarget) ";

		$eventidlist = array();

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($event_id);

			/* fetch values */
			while ($stmt->fetch()) {
				$eventidlist[] = $event_id;
			}
			/* close statement */
			$stmt->close();
		}

		#echo "$query <br>";

		if ( count($eventidlist) > 0 ) {
			$query  = "update _event_attendence ";
			$query .= "set devotee_id = $devIdTarget ";
			$query .= "where devotee_id = $devIdToRemove ";

			$query .= "and event_id ";
			$query .= "in (" ;
			$query .= join(',', array_values($eventidlist));
			$query .= ") ";

			#echo "$query <br>";
			if($stmt = self::$_conn->prepare($query)) {
				$stmt->execute();
				$stmt->close();
			}
		}


		$query  = "delete from _event_attendence ";
		$query  .= "where devotee_id = $devIdToRemove ";

		#echo "$query <br>";
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();
			$stmt->close();
		}

		$query  = "delete from _program_enrollment ";
		$query  .= "where devotee_id = $devIdToRemove ";

		#echo "$query <br>";
		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();
			$stmt->close();
		}

		# Update devotee_id` FROM _program_enrollment
		# SELECT e.`devotee_id` FROM _event_attendence e;

		return true;
	}

	private static function init () {
		if (   isset(self::$_conn) ) {
			return;
		}
		self::$_conn = DBConnection::getConnection();

		self::$_devotee = new Devotee();
	}
}




class DebugMessageProcessor
{
	private static $_dbugLevel;

	public static function printmessage ($message,$level) {
		if ( ! isset(self::$_dbugLevel) ) {
			self::$_dbugLevel = 10;
		}

		if ( $level >= self::$_dbugLevel ) {
			echo "<br>$message";
		}
	}

	public static function setDebugLevel($level) {
		self::$_dbugLevel = $level;
	}
}

class CentrePrograms
{
	private static $_conn;
	private static $_mapCentrePrograms;

	public static function isValidProgram ($centre, $program) {
		DebugMessageProcessor::printmessage("Calling CentrePrograms::isValidProgram ($centre, $program)",100);
		if ( ! isset(self::$_mapCentrePrograms) ) {
			self::init();
		}

		if ( ! isset(self::$_mapCentrePrograms[$centre]) ) {
			return false;
		}

		$programMap =  self::$_mapCentrePrograms[$centre];
		$programMap->dump();
		return $programMap->isValidValue($program);
	}

	public static function getPrograms ($centre) {
		DebugMessageProcessor::printmessage("Calling CentrePrograms::getPrograms ($centre)",100);
		if ( ! isset(self::$_mapCentrePrograms) ) {
			self::init();
		}

		if ( ! isset(self::$_mapCentrePrograms[$centre]) ) {
 			 throw new Exception("There are no programs listed against centre $centre");
 			 return;
		}

		return self::$_mapCentrePrograms[$centre];
	}

	private static function init() {
		self::$_conn = DBConnection::getConnection();

		$query = "SELECT centre_id,prog_id FROM _centre_program";

		if($stmt = self::$_conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($centre_id,$prog_id);

			/* fetch values */
			while ($stmt->fetch()) {
				if ( ! isset(self::$_mapCentrePrograms[$centre_id]) ) {
					$valMap = new AttributeMap();
					self::$_mapCentrePrograms[$centre_id] = $valMap;
				}

				$valMap = self::$_mapCentrePrograms[$centre_id];
				$valMap->setValue($prog_id);
			}

			/* close statement */
			$stmt->close();
		}
	}
}

/**
 *	AttributeMap
 *
 *	@category   Map of the properties and values
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class AttributeMap
{
	private $_valList ;
	public function isValidValue ($val) {
		$key = strtolower($val);

		if ( isset($this->_valList[$key]) ) {
			return true;
		} else {
			return false;
		}
	}

	public function getList () {
		return $this->_valList;
	}

	public function dump () {
		DebugMessageProcessor::printmessage(join('<br>',array_keys($this->_valList)),100);
	}

	public function setValue ($val) {
		$key = strtolower($val);

		$this->_valList[$key] = $val;
	}

	public function setKeyValue ($key,$val) {
		$this->_valList[$key] = $val;
	}

	public function getValue ($val) {
		$key = strtolower($val);

		if ( isset($this->_valList[$key]) ) {
			return $this->_valList[$key];
		} else {
			return $val;
		}
	}
}


class AttributeStore
{
	private static $conn;
	private static $_mapAttribStore;

	public static function isValidValue ($fldname, $fldval) {
		if ( ! isset(self::$_mapAttribStore) ) {
			self::init();
		}

		if ( ! isset(self::$_mapAttribStore[$fldname]) ) {
			return false;
		}

		$attrmap =  self::$_mapAttribStore[$fldname];
		return $attrmap->isValidValue($fldval);
	}

	public static function getValue ($fldname, $fldval) {
		if ( ! isset(self::$_mapAttribStore) ) {
			self::init();
		}

		if ( ! isset(self::$_mapAttribStore[$fldname]) ) {
			return $fldval;
		}

		$attrmap =  self::$_mapAttribStore[$fldname];
		return $attrmap->getValue($fldval);
	}

	private static function init() {
		self::$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or
					  die('There was a problem connecting to the database.');


		$query = "SELECT property,val FROM attribval";


		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($property,$val);

			/* fetch values */
			while ($stmt->fetch()) {
				if ( ! isset(self::$_mapAttribStore[$property]) ) {
					$valMap = new AttributeMap();
					self::$_mapAttribStore[$property] = $valMap;
				}

				$valMap = self::$_mapAttribStore[$property];
				$valMap->setValue($val);
			}

			/* close statement */
			$stmt->close();
		}
	}
}


/**
 *	EntityDef
 *
 *	@category   Entity Definition Containing key properties
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class EntityDef
{
    /**
     * Display field name to database field name map
     *
     * @var string,string
     */
	private $_mapDBName;

    /**
     * Display field name to field type map
     *
     * @var string
     */
	private $_mapType;

    /**
     * Display field name to validation key map
     *
     * @var string
     */
	private $_mapValidationKey;


	public function  getDBFldList() {
		return $this->_mapDBName;
	}

	public function  addField($displayName , $dbName, $fldtype, $validationKey) {
		$this->_mapDBName[$displayName] = $dbName;
		$this->_mapType[$displayName] = $fldtype;
		$this->_mapValidationKey[$displayName] = $validationKey;
	}

	public function dbName($displayName) {
        if ( isset($this->_mapDBName[$displayName]) ) {
            return $this->_mapDBName[$displayName];
 		} else {
 			 throw new Exception("Field: '$displayName' is not defined");
 		}
	}

	public function fldType($displayName) {
        if ( isset($this->_mapType[$displayName]) ) {
            return $this->_mapType[$displayName];
 		} else {
 			 throw new Exception("Field: '$displayName' is not defined");
 		}
	}

	public function validationKey($displayName) {
        if ( isset($this->_mapValidationKey[$displayName]) ) {
            return $this->_mapValidationKey[$displayName];
 		} else {
 			 throw new Exception("Field: '$displayName' is not defined");
 		}
	}
}

/**
 *	EntityMetaData
 *
 *	@category   Contains field definitions for all entities
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */

class EntityMetaData
{
    /**
     * Display field name to validation key map
     *
     * @var string,EntityDef
     */
	private static $_mapEntity2Def;
	private static $_conn;

	private static function init() {
		# Initializing Devotee
		self::$_conn = DBConnection::getConnection();
		$objDevoteeDef = new EntityDef();

		$objDevoteeDef->addField('Age', 'Age', 'text', 'Number:0:100');
		$objDevoteeDef->addField('AgeGroup', 'AgeGroup', 'list', 'Text');
		$objDevoteeDef->addField('Chanting', 'Chanting', 'list', 'Text');
		$objDevoteeDef->addField('Child Age', 'ChildAge', 'text', 'Text');
		$objDevoteeDef->addField('Child DoB', 'ChildDoB', 'text', 'Text');
		$objDevoteeDef->addField('ChildName', 'ChildName', 'text', 'Text');
		$objDevoteeDef->addField('Conn to ISKCON', 'ConntoISKCON', 'list', 'Text');
		$objDevoteeDef->addField('Conn To Other', 'ConnToOther', 'list', 'Text');
		$objDevoteeDef->addField('Education', 'Education', 'list', 'Text');
		$objDevoteeDef->addField('Ext Id', 'ExtId', 'text', 'Text');
		$objDevoteeDef->addField('FamilyId', 'FamilyId', 'text', 'Text');
		$objDevoteeDef->addField('ForiegnLang', 'ForiegnLang', 'list', 'Text');
		$objDevoteeDef->addField('Gender', 'Gender', 'list', 'Text');
		$objDevoteeDef->addField('Group Leader Name', 'GroupLeaderName', 'text', 'Text');
		$objDevoteeDef->addField('InitiatedName', 'InitiatedName', 'text', 'Text');
		$objDevoteeDef->addField('Interest', 'Interest', 'list', 'Text');
		$objDevoteeDef->addField('Kids', 'Kids', 'list', 'Text');
		$objDevoteeDef->addField('Language1', 'Language1', 'list', 'Text');
		$objDevoteeDef->addField('Language2', 'Language2', 'list', 'Text');
		$objDevoteeDef->addField('Language3', 'Language3', 'list', 'Text');
		$objDevoteeDef->addField('Locality', 'Locality', 'list', 'Text');
		$objDevoteeDef->addField('Married', 'Married', 'list', 'Text');
		$objDevoteeDef->addField('Nature of Work', 'NatureofWork', 'list', 'Text');
		$objDevoteeDef->addField('Other Spirit. Org.', 'OtherSpiritOrg', 'text', 'Text');
		$objDevoteeDef->addField('Perm Address', 'PermAddress', 'text', 'Text');
		$objDevoteeDef->addField('Preaching Group', 'PreachingGroup', 'text', 'Text');
		$objDevoteeDef->addField('Profession', 'Profession', 'list', 'Text');
		$objDevoteeDef->addField('Ref Src', 'RefSrc', 'list', 'Text');
		$objDevoteeDef->addField('RefBy', 'RefBy', 'text', 'Text');
		$objDevoteeDef->addField('Relation', 'Relation', 'list', 'Text');
		$objDevoteeDef->addField('Specific Work', 'SpecificWork', 'text', 'Text');
		$objDevoteeDef->addField('ISKCON Years', 'ISKCONYears', 'text', 'Number:1:100');
		$objDevoteeDef->addField('ISKCON Months', 'ISKCONMonths', 'text', 'Number:1:11');
		$objDevoteeDef->addField('Category', 'Category', 'list', 'Text');


		$objDevoteeDef->addField('AlternateContact', 'AlternateContactNo', 'text', 'ContactNo');
		$objDevoteeDef->addField('Mail', 'Mail', 'text', 'Mail');
		$objDevoteeDef->addField('Address', 'Address', 'text', 'Text');
		$objDevoteeDef->addField('SpouseName', 'SpouseName', 'text', 'Name');
		$objDevoteeDef->addField('Anniv', 'anniv', 'date', 'Text');
		$objDevoteeDef->addField('DoJ', 'Doj', 'date', 'Text');
		$objDevoteeDef->addField('DoB', 'dob', 'date', 'Text');
		$objDevoteeDef->addField('SpouseDoB', 'SpouseDoB', 'date', 'Text');

		self::$_mapEntity2Def['devotee'] = $objDevoteeDef;
	}

	public static function getEntityDef($entityName) {
		if (  ! isset(self::$_mapEntity2Def) ) {
			self::init();
		}

		if ( isset(self::$_mapEntity2Def[$entityName]) ) {
            return self::$_mapEntity2Def[$entityName];
 		} else {
 			 throw new Exception("Entity: '$entityName' is not defined");
 		}
	}
}



/**
 *	Devotee
 *
 *	@category   Devotee
 *	@package	Devotee
 *	@copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class Devotee
{
	private static $conn;
	private static $objDevoteeDef;


    /**
     * Devotee Name
     *
     * @var string
     */
    private $_name;

    /**
     * Devotee Contact Number
     *
     * @var string
     */
    private $_contact;


    /**
     * list of devotees which are registered with same contact number
     *
     * @var string
     */
    private $_otherWithSameCntct;

    /**
     * Devotee Update Commands which gets generated as different fields get added
     *
     * @var string
     */
    private $_updateCommands;

    /**
     * Error String which gets generated as different
     *
     * @var string
     */
    private $_errStr;

    /**
     * CentreId
     *
     * @var string
     */
    private $_centreId;
    private $_user;


    /**
     * Save Status
     *
     * @var string
     */
    private $_saveStatus;

	public function __construct() {
		self::$conn = DBConnection::getConnection();
		self::$objDevoteeDef = EntityMetaData::getEntityDef('devotee');
	}

	public static function getUnSpecified($centre_id,$prog_id,$attribute,$period) {
		self::$conn = DBConnection::getConnection();


		if ( $period > 0 ) {
			#$period =
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query = "select D.$attribute,count(distinct D.Id)";
			$query .= " FROM devotee D, _event_attendence  A ";
			$query .= " WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0 ";
			$query .= " AND A.event_id IN ( ";
			$query .= " SELECT id from _event WHERE ";
			$query .= " centre_id = $centre_id AND ";
			$query .= " prog_id = $prog_id AND ";
			$query .= " event_date >= $event_date) ";
			$query .= " group by D.$attribute ";
		} else {
			$query = "select D.$attribute,count(distinct  D.Id)";
			$query .= " FROM devotee D, _event_attendence  A ";
			$query .= " WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0 ";
			$query .= " AND A.event_id IN ( ";
			$query .= " SELECT id from _event WHERE ";
			$query .= " centre_id = $centre_id AND ";
			$query .= " prog_id = $prog_id ) ";
			$query .= " group by D.$attribute ";
		}



		$retval = 0;

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($atval, $count);

			/* fetch values */
			while ($stmt->fetch()) {
				if ($count > 0 ) {
					if (is_null($atval)) {
						$retval = $count;
					}
				}
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function expAbsenteeDev($centre_id,$prog_id,$event_date) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		$query  = " select D.Id,Name,InitiatedName,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender from devotee D,_event_attendence A,_event E ";
		$query  .= " WHERE E.event_date = $event_date AND E.centre_id=$centre_id and E.prog_id=$prog_id ";
		$query  .= " AND A.event_id = E.id ";
		$query  .= " AND A.devotee_id = D.Id   ";
		$query  .= " AND A.devotee_id not in ( ";
		$query  .= " select distinct B.devotee_id from _event_attendence B,_event E1 ";
		$query  .= " where B.event_id = E1.id ";
		$query  .= " AND E1.centre_id=$centre_id and prog_id=$prog_id ";
		$query  .= " AND E1.event_date > $event_date) ";

		$retval = "Id,Name,InitiatedName,Contact,AlternateContact,Mail,Address,SpouseName,Gender\n";

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$InitiatedName,$ContactNo,$AlternateContactNo,$Mail,$Address,$SpouseName,$Gender);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval .= "$Id,$Name,\"$InitiatedName\",$ContactNo,$AlternateContactNo,\"$Mail\",\"$Address\",$SpouseName,$Gender\n";
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function disable($name,$contact) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		$query  = " UPDATE  devotee ";
		$query  .= " SET isActive=0 ";
		$query  .= " WHERE Name='$name' ";
		$query  .= " AND ContactNo = '$contact'   ";

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}
	}

	public static function enable($devid) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"
	
		$query  = " UPDATE  devotee ";
		$query  .= " SET isActive=1 ";
		$query  .= "WHERE Id = ${devid} ";
		
		
		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}
	}
	
	public static function getAbsenteeDev($centre_id,$prog_id,$event_date,$fldlst) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		$query  = " select D.Id,Name,ContactNo,Mail,Address,AlternateContactNo,SpouseName,Gender,InitiatedName from devotee D,_event_attendence A,_event E ";
		$query  .= " WHERE E.event_date = $event_date AND E.centre_id=$centre_id and E.prog_id=$prog_id ";
		$query  .= " AND A.event_id = E.id ";
		$query  .= " AND A.devotee_id = D.Id   ";
		$query  .= " AND A.devotee_id not in ( ";
		$query  .= " select distinct B.devotee_id from _event_attendence B,_event E1 ";
		$query  .= " where B.event_id = E1.id ";
		$query  .= " AND E1.centre_id=$centre_id and prog_id=$prog_id ";
		$query  .= " AND E1.event_date > $event_date) ";

		$retval = array();

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$ContactNo,$Mail,$Address,$AlternateContactNo,$SpouseName,$Gender,$InitiatedName);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval[] =  array('Id' => $Id,
						'Name' => $Name,
						'Contact' => $ContactNo,
						'Mail' => $Mail,
						'Address' => $Address,
						'AlternateContact' => $AlternateContactNo,
						'SpouseName' => $SpouseName,
						'Gender' => $Gender,
						'InitiatedName' => $InitiatedName);
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function expEventDevNew($event_id) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"


		$query = "select D.Id,Name,InitiatedName,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender ";
		$query .= "from devotee D,_event_attendence A,_event E ";
		$query .= "WHERE E.id = $event_id ";
		$query .= "AND A.event_id = E.id ";
		$query .= "AND A.devotee_id = D.Id ";
		$query .= "AND A.devotee_id not in ( ";
		$query .= "select distinct B.devotee_id from _event_attendence B,_event E1 ";
		$query .= "where B.event_id = E1.id ";
		$query .= "AND E1.centre_id=E.centre_id and E1.prog_id=E.prog_id ";
		$query .= "AND E1.event_date < E.event_date ";
		$query .= ") ";

		$retval = "Id,Name,InitiatedName,Contact,AlternateContact,Mail,Address,SpouseName,Gender\n";

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$InitiatedName,$ContactNo,$AlternateContactNo,$Mail,$Address,$SpouseName,$Gender);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval .= "$Id,$Name,\"$InitiatedName\",$ContactNo,$AlternateContactNo,\"$Mail\",\"$Address\",$SpouseName,$Gender\n";
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function expEventDev($event_id) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		$query = "SELECT Id,Name,InitiatedName,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender ";
		$query .= "FROM devotee D, _event_attendence E ";
		$query .= "WHERE D.Id=E.devotee_id ";
		$query .= "AND E.event_id = $event_id ";

		$retval = "Id,Name,InitiatedName,Contact,AlternateContact,Mail,Address,SpouseName,Gender\n";

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$InitiatedName,$ContactNo,$AlternateContactNo,$Mail,$Address,$SpouseName,$Gender);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval .= "$Id,$Name,\"$InitiatedName\",$ContactNo,$AlternateContactNo,\"$Mail\",\"$Address\",$SpouseName,$Gender\n";
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function getEventDev($event_id,$fldlst) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		$query = "SELECT Id,Name,ContactNo,Mail,Address,AlternateContactNo,SpouseName,Gender,InitiatedName ";
		$query .= "FROM devotee D, _event_attendence E ";
		$query .= "WHERE D.Id=E.devotee_id ";
		$query .= "AND E.event_id = $event_id ";

		$retval = array();

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$ContactNo,$Mail,$Address,$AlternateContactNo,$SpouseName,$Gender,$InitiatedName);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval[] =  array('Id' => $Id,
						'Name' => $Name,
						'Contact' => $ContactNo,
						'Mail' => $Mail,
						'Address' => $Address,
						'AlternateContact' => $AlternateContactNo,
						'SpouseName' => $SpouseName,
						'Gender' => $Gender,
						'InitiatedName' => $InitiatedName);
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	private static function  getEventHeader($alleventdates) {
		$datesarray = array();
		foreach(array_keys($alleventdates) as $keyname) {
			$datesarray[] = date('d M Y', $alleventdates[$keyname]);
		}
		$aggevheader = self::getAgreegatedAttendenceHeader(4,2);
		return $aggevheader . "," . join(',',$datesarray);
	}

	private static function getDevAttExpRowVal($alleventdates,$devevents) {
		$attaray = array();

		foreach(array_keys($alleventdates) as $keyname) {
			if (array_key_exists($keyname,$devevents))
			{
				$attaray[] = 1;
			}
			else
			{
				$attaray[] = 0;
			}
		}
		return $attaray;
	}

	private static function getDevAttendence($devotee_id, $centre_id, $prog_id,$alleventdates) {
		$devevents = DevPMan::getDevEvents($devotee_id, $centre_id, $prog_id);
		$attendencearray = self::getDevAttExpRowVal($alleventdates, $devevents);
		$aggattarray = self::getAgreegatedAttendence(4,2,$centre_id, $prog_id,$devotee_id,$attendencearray);

		return join(',',$aggattarray) . "," . join(',',$attendencearray);
	}

	private static function getAgreegatedAttendenceHeader($agreegations,$gap) {
		return "Last2,Last4,Last6,Last8";
	}

	private static function getAgreegatedAttendence($agreegations,$gap,$centre_id, $prog_id,$devotee_id,$devevetlist) {
		$aggattarray = array();
		#$devtotal = DevPMan::getTotalAttendence($devotee_id, $centre_id, $prog_id,0);
		#$aggattarray[] = $devtotal[$devotee_id];

		$sum = 0;
		$iagreegations = 0;
		$igap = 0;

		foreach ($devevetlist as $value) {
			$sum += $value;
			$igap++;
			if ( $iagreegations < $agreegations ) {
				if ( $igap == $gap) {
					$aggattarray[]  = $sum;
					$iagreegations++;
					$igap = 0;
				}
			}
		}


		for ($i = $iagreegations; $i < $agreegations; $i++) {
			$aggattarray[] = "";
		}

		# Create a list of agreegated values
		return $aggattarray;
	}

	public static function GetDateFull ( $indate ) {
		$retval = date('d M', $indate);;
		$retval1 = date('d M Y', $indate);

		if ( $retval1 == '01 Jan 1970') {
			$retval = '';
		}

		if ( $indate == 0) {
			$retval = '';
		}

		return $retval;
	}


	public static function exportCentreProgDev($centre_id, $prog_id,$period) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"


		if ( $period > 0 ) {
			#$period =
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query = "SELECT distinct  D.Id,Name,InitiatedName,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender,dob,BirthMonth,anniv,AnnivMonth ";
			$query .= " FROM devotee D, _event_attendence  A ";
			$query .= " WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0 ";
			$query .= " AND A.event_id IN ( ";
			$query .= " SELECT id from _event WHERE ";
			$query .= " centre_id = $centre_id AND ";
			$query .= " prog_id = $prog_id AND ";
			$query .= " event_date >= $event_date) ";
		} else {
			$query = "SELECT Id,Name,InitiatedName,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender,dob,BirthMonth,anniv,AnnivMonth ";
			$query .= " FROM devotee D, _program_enrollment P ";
			$query .= " WHERE D.Id=P.devotee_id ";
			$query .= " AND D.isActive<>0 ";
			$query .= " AND P.centre_id = $centre_id ";
			$query .= " AND P.prog_id = $prog_id ";
		}

		# Get the event Id List
		# Create Header
		$eventlist = DevPMan::getEventList($centre_id, $prog_id, $period);
		$eventheader= self::getEventHeader($eventlist);

		$retval = "Id,Name,InitiatedName,Contact,AlternateContact,Mail,Address,SpouseName,Gender,BirthDay,BirthMonth,Anniversary,AnnivMonth,%Attendence,Total,3M,6M,9M,1Y,$eventheader\n";

		$basevaluemap = array();

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$InitiatedName,$ContactNo,$AlternateContactNo,$Mail,$Address,$SpouseName,$Gender,$BirthDay,$BirthMonth,$Anniversary,$AnnivMonth);


			/* fetch values */
			while ($stmt->fetch()) {
				$sBirthDay = self::GetDateFull($BirthDay);
				$sAnniversary = self::GetDateFull($Anniversary);
				$basevaluemap[$Id]="$Id,$Name,\"$InitiatedName\",$ContactNo,$AlternateContactNo,\"$Mail\",\"$Address\",$SpouseName,$Gender,$sBirthDay,$BirthMonth,$sAnniversary,$AnnivMonth";
			}
			/* close statement */
			$stmt->close();
		}

		#getDevAttendence
		$mapDevTotalPresense = DevPMan::getTotalAttendence($centre_id, $prog_id,0);
		$mapDev3MPresense = DevPMan::getTotalAttendence($centre_id, $prog_id,3);
		$mapDev6MPresense = DevPMan::getTotalAttendence($centre_id, $prog_id,6);
		$mapDev9MPresense = DevPMan::getTotalAttendence($centre_id, $prog_id,9);
		$mapDev12MPresense = DevPMan::getTotalAttendence($centre_id, $prog_id,12);

		$total_event_count = DevPMan::getTotalEventCount($centre_id, $prog_id);
		$attendence_offset_map = DevPMan::getAttendenceOffset($centre_id, $prog_id);

		foreach(array_keys($basevaluemap) as $Id) {

			if (array_key_exists($Id,$attendence_offset_map))
			{
				$offset = $attendence_offset_map[$Id];
			}
			else
			{
				$offset = DevPMan::setAttendenceOffset($Id, $centre_id, $prog_id);
			}

			$events_after_registration = $total_event_count - $offset;

			$devattendence = self::getDevAttendence($Id, $centre_id, $prog_id, $eventlist);
			$retval .= $basevaluemap[$Id];

			$DevTotalPresense = 0;
			if (array_key_exists($Id,$mapDevTotalPresense))
			{
				$DevTotalPresense =  $mapDevTotalPresense[$Id];
			}

			$presence_pct = ($DevTotalPresense * 100)/$events_after_registration;
			$presence_pct = round($presence_pct,0,PHP_ROUND_HALF_UP);
			$retval .= ",${presence_pct},$DevTotalPresense";


			if (array_key_exists($Id,$mapDev3MPresense))
			{
				$retval .= "," . $mapDev3MPresense[$Id];
			}
			else
			{
				$retval .= ",0";
			}

			if (array_key_exists($Id,$mapDev6MPresense))
			{
				$retval .= "," . $mapDev6MPresense[$Id];
			}
			else
			{
				$retval .= ",0";
			}

			if (array_key_exists($Id,$mapDev9MPresense))
			{
				$retval .= "," . $mapDev9MPresense[$Id];
			}
			else
			{
				$retval .= ",0";
			}

			if (array_key_exists($Id,$mapDev12MPresense))
			{
				$retval .= "," . $mapDev12MPresense[$Id];
			}
			else
			{
				$retval .= ",0";
			}

			$retval .= ",$devattendence\n";
		}

		return $retval;
	}

	public static function expRegDevCntreProgAttrib($centre_id, $prog_id, $attribute,$attribval,$period) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query = "SELECT distinct Id,Name,InitiatedName ,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender ";
			$query .= "FROM devotee D, _event_attendence  A ";
			$query .= "WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0  ";
			$query .= "AND A.event_id IN ( ";
			$query .= "SELECT id from _event WHERE ";
			$query .= "centre_id = $centre_id AND ";
			$query .= "prog_id = $prog_id AND ";
			$query .= "event_date >= $event_date) ";
		} else {
			$query = "SELECT distinct Id,Name,InitiatedName ,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender ";
			$query .= "FROM devotee D, _event_attendence  A ";
			$query .= "WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0  ";
			$query .= "AND A.event_id IN ( ";
			$query .= "SELECT id from _event WHERE ";
			$query .= "centre_id = $centre_id AND ";
			$query .= "prog_id = $prog_id AND ";
			$query .= "event_date >= 0) ";
		}

		if ( $attribval == "null" ) {
			$query .= "AND $attribute is null";
		} else {
			$query .= "AND $attribute='$attribval' ";
		}

		$retval = "Id,Name,InitiatedName,Contact,AlternateContact,Mail,Address,SpouseName,Gender\n";

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$InitiatedName,$ContactNo,$AlternateContactNo,$Mail,$Address,$SpouseName,$Gender);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval .= "$Id,$Name,\"$InitiatedName\",$ContactNo,$AlternateContactNo,\"$Mail\",\"$Address\",$SpouseName,$Gender\n";
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public static function getRegDevCntreProgAttrib($centre_id, $prog_id, $attribute,$attribval,$fldlst,$period) {
		self::$conn = DBConnection::getConnection();
		#"Name","InitiatedName","Contact","AlternateContact","Mail","Address","SpouseName","Gender"

		if ( $period > 0 ) {
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query = "SELECT distinct Id,Name,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender,InitiatedName ";
			$query .= "FROM devotee D, _event_attendence  A ";
			$query .= "WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0  ";
			$query .= "AND A.event_id IN ( ";
			$query .= "SELECT id from _event WHERE ";
			$query .= "centre_id = $centre_id AND ";
			$query .= "prog_id = $prog_id AND ";
			$query .= "event_date >= $event_date) ";
		} else {
			$query = "SELECT distinct Id,Name,ContactNo,AlternateContactNo,Mail,Address,SpouseName,Gender,InitiatedName ";
			$query .= "FROM devotee D, _event_attendence  A ";
			$query .= "WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0  ";
			$query .= "AND A.event_id IN ( ";
			$query .= "SELECT id from _event WHERE ";
			$query .= "centre_id = $centre_id AND ";
			$query .= "prog_id = $prog_id AND ";
			$query .= "event_date >= 0) ";
		}

		if ( $attribval == "null" ) {
			$query .= "AND $attribute is null";
		} else {
			$query .= "AND $attribute='$attribval' ";
		}

		$retval = array();

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($Id,$Name,$ContactNo,$Mail,$Address,$AlternateContactNo,$SpouseName,$Gender,$InitiatedName);

			/* fetch values */
			while ($stmt->fetch()) {
					$retval[] =  array('Id' => $Id,
						'Name' => $Name,
						'Contact' => $ContactNo,
						'Mail' => $Mail,
						'Address' => $Address,
						'AlternateContact' => $AlternateContactNo,
						'SpouseName' => $SpouseName,
						'Gender' => $Gender,
						'InitiatedName' => $InitiatedName);
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;

	}


	public static function getDistribution($centre_id,$prog_id,$attribute,$period) {
		self::$conn = DBConnection::getConnection();

		if ( $period > 0 ) {
			#$period =
			$today = strtotime(date("Y/m/d"));
			$event_date = $today - ($period * 31 * 24 * 60 * 60 );

			$query = "select D.$attribute,count(distinct D.Id)";
			$query .= " FROM devotee D, _event_attendence  A ";
			$query .= " WHERE D.Id=A.devotee_id ";
			$query .= " AND D.isActive<>0  ";
			$query .= " AND A.event_id IN ( ";
			$query .= " SELECT id from _event WHERE ";
			$query .= " centre_id = $centre_id AND ";
			$query .= " prog_id = $prog_id AND ";
			$query .= " event_date >= $event_date) ";
			$query .= " group by D.$attribute ";
		} else {
			$query = "select $attribute,count(distinct A.Id) from devotee A,_program_enrollment P ";
			$query .= "where P.devotee_id= A.Id ";
			$query .= " AND D.isActive<>0  ";
			$query .= "AND P.centre_id=$centre_id and P.prog_id=$prog_id ";
			$query .= "group by A.$attribute ";
		}


		$retval = array();

		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($atval, $count);

			/* fetch values */
			while ($stmt->fetch()) {
				if ($count > 0 ) {
					if ( !is_null($atval)) {
						$retval[] = array('Property' => $atval, 'Count' => $count);
					}
				}
			}
			/* close statement */
			$stmt->close();
		}
		return $retval;
	}

	public function get_dev_count () {
		$retval = '';
		$query = "select count(*) devCount from devotee";


		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devCount);

			/* fetch values */
			while ($stmt->fetch()) {
				$retval .= $devCount;
			}

			/* close statement */
			$stmt->close();
		}

		return $retval;

	}

	public function dump() {
		echo "<br><br><br>Name: ", $this->_name, " -Contact: " , $this->_contact;
		echo "<br>Update Commands: ", join(',',$this->_updateCommands);
		echo "<br>Errors: ", join(',',$this->_errStr);
	}

	public function clear() {
		unset($this->_name);
		unset($this->_contact);
		unset($this->_updateCommands);
		unset($this->_errStr);
		unset($this->_otherWithSameCntct);
		unset($this->_saveStatus);
	}

	public function setContext($centreId,$user) {
		$this->_centreId = $centreId ;
		$this->_user = $user ;
		#echo "<br>CentreId = $this->_centreId";
		#echo "<br>User = $this->_user";
	}

	public function getSaveStatus() {
		return $this->_saveStatus;
	}

	public function getDataErrors() {
		return $this->_errStr;
	}

	public function init($name, $contact) {
		# Set member variables for name and contact after performing the changes to name and validating the contact
		$this->_updateCommands = array();
		$this->_errStr = array();
		$this->_otherWithSameCntct = array();
		$this->_saveStatus = false;

		$this->_name = $this->validateInput( 'Name', $name);
		if ( strlen($this->_name) < 1 ) {
			$this->_errStr[] = "Invalid Name: $name";
		}


		if (! $this->validateInput('ContactNo',$contact) ) {
			$this->_errStr[] = "Invalid contact number: $contact";
		} else {
			$this->_contact = $contact;
		}
	}


	public function save($ignoreDuplicate) {
		if ( ! isset($this->_contact) ) {
			return;
		}

		if ( strlen($this->_name) < 1 ) {
			return;
		}

		$this->other_with_same_contact();
		$others = count($this->_otherWithSameCntct);

		$foundotherstable = "<table border>";
		$foundotherstable .= "<tr><td>Name</td><td>Contact</td><td>AlternateContact</td></tr>";
		$foundotherstable .= join('',$this->_otherWithSameCntct);
		$foundotherstable .= "</table>";

		if ( !$ignoreDuplicate && ($others > 0) ) {
			#$this->_errStr[] = "Skipping Save: Other devotees with same contact are - " . join(',',$this->_otherWithSameCntct);;
			$this->_errStr[] = "Skipping Save: Other devotees with same contact - <br>" . $foundotherstable;
			return;
		}

		if ( $others > 0 ) {
			#$this->_errStr[] = "There are other devotees with same contact - " . join(',',$this->_otherWithSameCntct);;
			$this->_errStr[] = "There are other devotees with same contact - " . $foundotherstable;
		}


		# Create new record if not already exists
		$this->addDevKey();


		# Update with extra atttributes
		$devId = $this->getDevId();

		if ( $devId < 0 ) {
			$this->_errStr[] = "Failed to register devotee with Name: $this->_name and Contact: $this->_contact";
			return;
		}

		$this->_saveStatus = true;

		#$this->_updateCommands[] = "CentreId=$this->_centreId";

		$today = strtotime(date("Y/m/d"));

		if ( count($this->_updateCommands) > 0 ) {
			$sql_str = "UPDATE devotee set " .  join(',',$this->_updateCommands) . " ,updated='$today',updatedby= '" . $this->_user . "' WHERE Id=$devId";
			#echo "<br>$sql_str";
			if($stmt = self::$conn->prepare($sql_str)) {
				$stmt->execute();

				/* close statement */
				$stmt->close();
			}
		}

		$this->PersistDevImpliedAttrib ($devId);
		# If there are others with same contact number report them in errors
	}

	private function GetMonth ( $indate ) {
			$retval = date('d M', $indate);;
			$retval1 = date('d M Y', $indate);

			if ( $retval1 == '01 Jan 1970') {
				$retval = '';
			}

			if ( $indate == 0) {
				$retval = '';
			}

			if ( $retval != '' ) {
				$retval = date('M', $indate);;
			}

		return $retval;
	}


	private function PersistDevImpliedAttrib ($Id) {
		$query = "select dob,anniv,SpouseDoB from devotee WHERE Id=$Id";

		$altDob = "";
		$altAnniv = "";
		$altSpouseDoB = "";
		$field_array = array();


		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($dob,$anniv,$SpouseDoB);

			/* fetch values */
			while ($stmt->fetch()) {
				$altDob = $this->GetMonth($dob);
				$altAnniv = $this->GetMonth($anniv);
				$altSpouseDoB = $this->GetMonth($SpouseDoB);

				if ( $altDob != '' ) {
					$field_array[] = "BirthMonth='$altDob'";
				}

				if ( $altAnniv != '' ) {
					$field_array[] = "AnnivMonth='$altAnniv'";
				}

				if ( $altSpouseDoB != '' ) {
					$field_array[] = "BirthMonthSpouse='$altSpouseDoB'";
				}

			}

			/* close statement */
			$stmt->close();
		}

		if ( sizeof($field_array) > 0 ) {
			$sql_str = "UPDATE devotee set " . join(',',$field_array) . " WHERE Id=$Id";
			#echo "<br>$sql_str";
			if($stmt = self::$conn->prepare($sql_str)) {
				$stmt->execute();

				/* close statement */
				$stmt->close();
			}
		}
	}

	public function setField($fldname, $fldval) {
		if ( strlen($fldval) < 1 ) {
			return;
		}

		try {
			$FieldType = self::$objDevoteeDef->fldType($fldname);
			$FieldDBName = self::$objDevoteeDef->dbName($fldname);
			$FieldValidation = self::$objDevoteeDef->validationKey($fldname);

			if ( $FieldType == 'list') {
				if ( AttributeStore::isValidValue($fldname,$fldval)) {
					if ( strlen($fldval) > 0 ) {
						$fmtVal = AttributeStore::getValue($fldname,$fldval);
						$this->_updateCommands[] = "$FieldDBName='$fmtVal'";
					}
				} else {
					$this->_errStr[] = "$fldname: Invalid Value \"$fldval\"";
				}
			}

			if ( $FieldType == 'text') {
				if ( $this->isValidValue( $FieldValidation, $fldval) ) {
					$fldval = $this->fmtValue( $FieldValidation, $fldval);
					if ( strlen($fldval) > 0 ) {
						$this->_updateCommands[] = "$FieldDBName='$fldval'";
					}
				} else {
					$this->_errStr[] = "$fldname: Invalid Value \"$fldval\"";
				}
			}

			if ( $FieldType == 'date') {

				$fldval = strtotime(trim($fldval));
				if ( strlen($fldval) > 0 ) {
					$this->_updateCommands[] = "$FieldDBName=$fldval";
				}
			}

		} catch( Exception $e ) {
			$this->_errStr[] = $e->getMessage();
		}
	}


	private function other_with_same_contact() {
		$query = "select Name,ContactNo,AlternateContactNo from devotee WHERE (ContactNo = '$this->_contact' OR AlternateContactNo='$this->_contact') AND Name != '$this->_name'";

		$found = 0;
		if($stmt = self::$conn->prepare($query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devName,$ContactNo,$AlternateContactNo);

			/* fetch values */
			while ($stmt->fetch()) {
				$this->_otherWithSameCntct[] = "<tr><td>$devName</td><td>$ContactNo</td><td>$AlternateContactNo</td></tr>";
				$found = $found + 1;
			}

			/* close statement */
			$stmt->close();
		}

		if ( $found > 0 ) {
			DebugMessageProcessor::printmessage("<br>$query<br>", 10);
		}
	}

	public function findDevId ($name , $contact) {
		$sql_query = "SELECT Id FROM devotee WHERE Name= '$name' and ContactNo='$contact'";
		$devid = -1;

		if($stmt = self::$conn->prepare($sql_query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devidNew);

			/* fetch values */
			while ($stmt->fetch()) {
				$devid = $devidNew;
			}

			/* close statement */
			$stmt->close();
		}

		return $devid;
	}

	public function getDevId () {
		if ( ! isset($this->_contact) ) {
			return -1;
		}

		$sql_query = "SELECT Id FROM devotee WHERE Name= '$this->_name' and ContactNo='$this->_contact'";
		$devid = -1;

		if($stmt = self::$conn->prepare($sql_query)) {
			$stmt->execute();

			/* bind result variables */
			$stmt->bind_result($devidNew);

			/* fetch values */
			while ($stmt->fetch()) {
				$devid = $devidNew;
			}

			/* close statement */
			$stmt->close();
		}

		return $devid;
	}

	public function Merge($devIdToRemove,$devIdTarget) {

		# update all fields from devotee to remove to $devIdTarget
		# remove devotee with $devIdToRemove
		foreach ( self::$objDevoteeDef->getDBFldList() as $dbfld ) {

			$sql_query = "UPDATE devotee dt1, devotee dt2 ";
			$sql_query .= "SET dt1.${dbfld} = dt2.${dbfld} ";
			$sql_query .= "WHERE dt1.Id = ${devIdTarget} and dt2.Id = ${devIdToRemove} AND dt1.${dbfld} is null ";

			if($stmt = self::$conn->prepare($sql_query)) {
				$stmt->execute();
				/* close statement */
				$stmt->close();
			}

			#echo "<br>$sql_query";
		}

		DevPMan::Merge($devIdToRemove, $devIdTarget);

		$sql_query = "DELETE FROM devotee ";
		$sql_query .= "WHERE Id = ${devIdToRemove} ";

		if($stmt = self::$conn->prepare($sql_query)) {
			$stmt->execute();
			/* close statement */
			$stmt->close();
		}

		self::enable($devIdTarget);
		
		return true;
	}

	private function addDevKey () {
		$devid = $this->getDevId();

		$today = strtotime(date("Y/m/d"));

		if ( $devid < 0 ) {
			$sql_query = "INSERT INTO devotee (Name,ContactNo,CentreId,Doj,created,addedby,updated,updatedby) VALUES ('$this->_name','$this->_contact',$this->_centreId,$today,$today,'$this->_user',$today,'$this->_user')";
			#echo "<br>$sql_query";
			if($stmt = self::$conn->prepare($sql_query)) {
				$stmt->execute();

				/* close statement */
				$stmt->close();
			}
		}
	}

	private function isValidListItem( $fldname, $fldval) {
		AttributeStore::isValidValue($fldname,$fldval);
	}

	private function isValidValue( $input_type, $input_val) {
		switch ($input_type) {
		case "UserId":
			if( preg_match('/^[a-z_A-Z0-9]*$/',$input_val) ){
				return true;
			}
			else{
				return false;
			}
			break;
		case "Mail":
			if( !preg_match('/^(([\-\w]+)\.?)+@(([\-\w]+)\.?)+\.[a-zA-Z]{2,4}$/',$input_val) ){
				return false;
			}
			else{
				return true;
			}
			break;
		case "ContactNo":
			if( preg_match('/^[1-9][0-9]{9}$/',$input_val) ){
				return true;
			}
			else{
				return false;
			}
			break;
		}
		return true;
	}

	private function fmtValue( $input_type, $input_val) {
		switch ($input_type) {
		case "Name":
			$input_val = strtolower (preg_replace('/[^a-zA-Z\s]/', '', $input_val));
			$input_val = preg_replace('/\s\s+/', ' ',$input_val);
			return $input_val;
			break;
		case "text_value":
			$input_val = str_replace(",","/",$input_val);
			$input_val = preg_replace('/\s\s+/', ' ',$input_val);
			return strtolower (preg_replace('/[^a-zA-Z0-9\-\/\s]/', '', $input_val));
			break;
		case "Text":
			$input_val = str_replace(",","/",$input_val);
			$input_val = preg_replace('/\s\s+/', ' ',$input_val);
			return strtolower (preg_replace('/[^a-zA-Z0-9\-\/\s]/', '', $input_val));
			break;
		}
		return $input_val;
	}


	private function validateInput( $input_type, $input_val) {
		switch ($input_type) {
		case "Password":
			return isStrongPassword ($input_val);
			break;
		case "UserId":
			if( preg_match('/^[a-z_A-Z0-9]*$/',$input_val) ){
				return true;
			}
			else{
				return false;
			}
			break;
		case "Mail":
			return true;
			if( !preg_match('/^(([\-\w]+)\.?)+@(([\-\w]+)\.?)+\.[a-zA-Z]{2,4}$/',$input_val) ){
				return false;
			}
			else{
				return true;
			}
			break;
		case "ContactNo":
			if( preg_match('/^[1-9][0-9]{9}$/',$input_val) ){
				return true;
			}
			else{
				return false;
			}
			break;
		case "Name":
			$input_val = strtolower (preg_replace('/[^a-zA-Z\s]/', '', $input_val));
			$input_val = preg_replace('/\s\s+/', ' ',$input_val);
			return $input_val;
			break;
		case "text_value":
			$input_val = str_replace(",","/",$input_val);
			$input_val = preg_replace('/\s\s+/', ' ',$input_val);
			return strtolower (preg_replace('/[^a-zA-Z0-9\-\/\s]/', '', $input_val));
			break;
		}
		return true;
	}

}


class DevoteeImporter
{
	private static function getFormattedExcelCellVal ($excelSheet, $column,$row,$fldtype) {
		$ValueField = $excelSheet->getCellByColumnAndRow($column,$row);
		$value = trim($ValueField->getValue());

		switch ($fldtype) {
			case "text":
				break;
			case "date":
				$value = PHPExcel_Style_NumberFormat::toFormattedString($value, "YYYY/MM/DD");
				break;
		}

		return $value;
	}

	public static function importAttendenceFromExcel ($excelFile,$CentreId,$ProgramId,$user) {
		$objPHPExcel = PHPExcel_IOFactory::load($excelFile);
		$excelActiveSheet = $objPHPExcel->getActiveSheet();
		echo '<h1>Importing Attendence from sheet - ' , $excelActiveSheet->getTitle() , '</h1>' ,EOL;;

		if ( ! CentrePrograms::isValidProgram($CentreId, $ProgramId) ) {
			echo "<hr><span style=\"background-color:red\">Invalid Centre: $Centre & Program: $Program pair</span>";
			return;
		}

		# Create headermap
		$stillInHeader = 1;
		$counter = 2;
		while ( $stillInHeader ) {
			$curcell = self::getFormattedExcelCellVal($excelActiveSheet,$counter,1,'date');

			if ( strlen($curcell) < 1 ) {
				$stillInHeader = 0;
			} else {
				$FieldIndex[$curcell] = $counter;
				$eventdate = strtotime($curcell);
				DevPMan::registerEvent($CentreId, $ProgramId, $eventdate);
			}
			$counter++;
		}

		$stillInData = 1;
		$counter = 2;
		$NameIndex = 0;
		$ContactIndex = 1;
		$MissingCounter = 0;

		$IgnoreSameContact = false;
		if ( $_POST['IgnoreSameContact'] > 0 ) {
			$IgnoreSameContact = true;
		}

		$objDevotee = New Devotee();
		$objDevotee->setContext($CentreId,$user);

		while ( $stillInData ) {
				$objDevotee->clear();
				$NameVal = self::getFormattedExcelCellVal($excelActiveSheet,$NameIndex,$counter,'text');
				$Contact = self::getFormattedExcelCellVal($excelActiveSheet,$ContactIndex,$counter,'text');

				if ( strlen($Contact) < 1 ) {
					$MissingCounter++;
					if ( $MissingCounter > 5 ) {
						$stillInData = 0;
					}
				} else {
					$MissingCounter = 0;
					$objDevotee->init($NameVal, $Contact);
					$objDevotee->save($IgnoreSameContact);
					if ( $objDevotee->getSaveStatus() )
					{
						foreach(array_keys($FieldIndex) as $FieldName) {
							try {
								$eventdate = strtotime($FieldName);
								$eventid = DevPMan::getEventId($CentreId,$ProgramId, $eventdate);
								$FieldValue = self::getFormattedExcelCellVal($excelActiveSheet,$FieldIndex[$FieldName],$counter,'text');
								$key = strtolower($FieldValue);
								if ( $key == 'p' ) {
									DevPMan::logAttendence($objDevotee->getDevId(), $eventid, $CentreId,$ProgramId,$IgnoreSameContact);
								}
							} catch( Exception $e ) {
								echo "<br>" . $e->getMessage();
							}
						}
						$status = "<span style=\"background-color:green\">OK</span>";
					}
					else
					{
						$status = "<span style=\"background-color:red\">FAIL</span>";
					}
					$dataErrors = $objDevotee->getDataErrors();
					echo "<hr><br>Import Status: $status<br>Name - $NameVal, Contact - $Contact<br>" ;
					echo "<span style=\"background-color:yellow\">" . join('<br>',$dataErrors) . "</span>";
				}
			$counter++;
		}
	}

	public static function importFromExcel ($excelFile,$Centre,$User) {
		$objPHPExcel = PHPExcel_IOFactory::load($excelFile);
		$excelActiveSheet = $objPHPExcel->getActiveSheet();

		$successfeed = '';
		$failfeed = '';


		echo '<h1>Importing devotees from sheet - ' , $excelActiveSheet->getTitle() , '</h1>' ,EOL;;

		# Create headermap
		$stillInHeader = 1;
		$counter = 0;
		while ( $stillInHeader ) {
			$curcell = $excelActiveSheet->getCellByColumnAndRow($counter,1);
			$curcell = $curcell->getValue();
			$curcell = trim($curcell);

			if ( strlen($curcell) < 1 ) {
				$stillInHeader = 0;
			} else {
				$FieldIndex[$curcell] = $counter;
			}
			$counter++;
		}

		$stillInData = 1;
		$counter = 2;
		$NameIndex = $FieldIndex['Name'];
		$ContactIndex = $FieldIndex['Contact'];
		$MissingCounter = 0;

		$objDevotee = New Devotee();
		$objDevoteeDef = EntityMetaData::getEntityDef('devotee');

		$objDevotee->setContext($Centre,$User);

		$IgnoreSameContact = false;
		if ( $_POST['IgnoreSameContact'] > 0 ) {
			$IgnoreSameContact = true;
		}

		while ( $stillInData ) {
			# Initialize the devotee object
			$objDevotee->clear();

			$NameVal = self::getFormattedExcelCellVal($excelActiveSheet,$NameIndex,$counter,'text');
			$Contact = self::getFormattedExcelCellVal($excelActiveSheet,$ContactIndex,$counter,'text');
			$objDevotee->init($NameVal,$Contact);

			if ( strlen($Contact) < 1 ) {
				$MissingCounter++;
				if ( $MissingCounter > 5 ) {
					$stillInData = 0;
				}
			} else {
				$MissingCounter = 0;

				foreach(array_keys($FieldIndex) as $FieldName) {

				try {
					$FieldType = $objDevoteeDef->fldType($FieldName);
					$FieldValue = self::getFormattedExcelCellVal($excelActiveSheet,$FieldIndex[$FieldName],$counter,$FieldType);
					$objDevotee->setField($FieldName,$FieldValue);
				} catch( Exception $e ) {
					#print $e->getMessage();
				}
			}

			$objDevotee->save($IgnoreSameContact);
			#$objDevotee->dump();
			$dataErrors = $objDevotee->getDataErrors();

			if ( $objDevotee->getSaveStatus() ) {
					$status = "<span style=\"background-color:green\">OK</span>";
					$successfeed .= "<hr><br>Import Status: $status<br>Name - $NameVal, Contact - $Contact<br>" ;
			} else {
					$status = "<span style=\"background-color:red\">FAIL</span>";
					$failfeed .= "<hr><br>Import Status: $status<br>Name - $NameVal, Contact - $Contact<br>" ;
					$failfeed .= "<span style=\"background-color:yellow\">" . join('<br>',$dataErrors) . "</span>";
					#echo "<hr><br>Import Status: $status<br>Name - $NameVal, Contact - $Contact<br>" ;
					#echo "<span style=\"background-color:yellow\">" . join('<br>',$dataErrors) . "</span>";
			}
			}
			$counter++;
		}
		echo $successfeed;
		echo $failfeed;
	}

	public static function disableDevoteesFromExcel ($excelFile) {
		$objPHPExcel = PHPExcel_IOFactory::load($excelFile);
		$excelActiveSheet = $objPHPExcel->getActiveSheet();
		echo '<h1>Disabling Devotees from sheet - ' , $excelActiveSheet->getTitle() , '</h1>' ,EOL;;

		$stillInData = 1;
		$counter = 2;
		$MissingCounter = 0;

		while ( $stillInData ) {
			$oldName = self::getFormattedExcelCellVal($excelActiveSheet,0,$counter,'text');
			$oldContact = self::getFormattedExcelCellVal($excelActiveSheet,1,$counter,'text');

			if ( strlen($oldContact) < 1 ) {
				$MissingCounter++;
				if ( $MissingCounter > 5 ) {
					$stillInData = 0;
				}
			} else {
				$MissingCounter = 0;
				echo "Processing: $oldName - $oldContact <br>";
				Devotee::disable($oldName,$oldContact);
			}
			$counter++;
		}
	}

	public static function merge ($excelFile,$CentreId, $ProgramId,$user) {
		$objPHPExcel = PHPExcel_IOFactory::load($excelFile);
		$excelActiveSheet = $objPHPExcel->getActiveSheet();
		echo '<h1>Merging Devotees From sheet - ' , $excelActiveSheet->getTitle() , '</h1>' ,EOL;;

		if ( ! CentrePrograms::isValidProgram($CentreId, $ProgramId) ) {
			echo "<hr><span style=\"background-color:red\">Invalid Centre: $Centre & Program: $Program pair</span>";
			return;
		}

		$stillInData = 1;
		$counter = 2;
		$MissingCounter = 0;

		$objDevoteeOld = New Devotee();
		$objDevoteeOld->setContext($CentreId,$user);

		$objDevoteeNew = New Devotee();
		$objDevoteeNew->setContext($CentreId,$user);

		$IgnoreSameContact = true;
		while ( $stillInData ) {
			$objDevoteeOld->clear();
			$objDevoteeNew->clear();

			$oldName = self::getFormattedExcelCellVal($excelActiveSheet,0,$counter,'text');
			$oldContact = self::getFormattedExcelCellVal($excelActiveSheet,1,$counter,'text');
			$newName = self::getFormattedExcelCellVal($excelActiveSheet,2,$counter,'text');
			$newContact = self::getFormattedExcelCellVal($excelActiveSheet,3,$counter,'text');

			if ( strlen($oldContact) < 1 ) {
				$MissingCounter++;
				if ( $MissingCounter > 5 ) {
					$stillInData = 0;
				}
			} else {
				$MissingCounter = 0;

				$objDevoteeOld->init($oldName, $oldContact);
				$objDevoteeNew->init($newName, $newContact);

				$oldId = $objDevoteeOld->findDevId($oldName, $oldContact);
				$NewId = $objDevoteeNew->getDevId();

				if ( $oldId > 0 && $NewId < 0 ) {
					# save new devotee
					$objDevoteeNew->save($IgnoreSameContact);
					$NewId = $objDevoteeNew->getDevId();
				}

				if ( $oldId < 0 || $NewId < 0 ) {
					echo "<br>$oldName , $oldContact , $newName, $newContact - |$oldId|$NewId| : FAIL ";
				} else {
					echo "<br>$oldName , $oldContact , $newName, $newContact : OK ";
					$objDevoteeNew->Merge($oldId,$NewId);
					DevPMan::Merge($oldId,$NewId);
				}
			}
			$counter++;
		}
	}
}
