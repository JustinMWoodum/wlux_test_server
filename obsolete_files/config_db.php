<?php
$db_selected = NULL;
$link = NULL;

function open_db () {
	$retVal = 0;
    $DB_SERVER = 'localhost';
	print ('Opening: '.$DB_SERVER.' for '.$DB_USER.'\n');

	$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS);
	if (!$link) {
		$retVal = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER.'--Error: ' . mysql_error().'\n';
	}
	return $retVal;
}

function read_config_db ($arg_study, $arg_session) {
	$retData = NULL;
	// select the config db
	print ('Selecting: '.$DB_DATABASE_NAME.'\n');
	$db_selected = mysqli_select_db($DB_DATABASE_NAME, $link);
	if (!$db_selected) {
		die ('Can\'t access database: '.$DB_DATABASE_NAME.'--Error: ' . mysql_error().'\n');
		$db_selected = NULL;
	}
	 
	if (NULL != $db_selected) {
		$query_string = 'SELECT TOP 1 * FROM '.$DB_TABLE_SESSION_CONFIG.' WHERE studyId = '.$arg_study;
		print ('QueryString: '.$query_string);
    	$result = mysqli_query ($query_string);
		while ($thisRecord = mysqli_fetch_assoc($result)) 
		{
     		$retData = $thisRecord;
		}
	} 
	return $retData;
}

function close_db() {
	if (NULL != $link) {
		mysqli_close($link);
		$link = NULL;
	}
}

?>