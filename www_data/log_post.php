<?php
function _log_post ($link, $postData) {
require 'config_files.php';	
	// add record to the appropriate log table 
	// determine log type from variable name
	$logType = 'open';
	if (!empty($postData[$logType])) {
		$logData = $postData[$logType];
		// $logData contains an open data block
		$logTable = $DB_TABLE_OPEN_LOG;
		// TODO: Validate fields
	} else {
		// $logData contains an transition data block
		$logType = 'transition';
		if (!empty($postData[$logType])) {
			$logData = $postData[$logType];
			// process transition log request
			$logTable = $DB_TABLE_TRANSITION_LOG;
			// TODO: Validate fields
		} else {
			// unrecognized command
			$errData['message'] = 'Log type not recognized. Log type must be \'open\' or \'transition\'';
			$errData['postData'] = $postData;
			$errData['getData'] = $_GET;
			//$errData['globals'] = $GLOBALS;
			$response['error'] = $errData;
		}
	}
	
	if (!empty($logTable)) {
		// process the log request
		// make query string from the data structure 
		
		// add server-generated fields to insert query
		$dbColList = 'recordSeq, serverTimestamp, recordType';
		$dbValList = 'NULL, CURRENT_TIMESTAMP, \''.$logType.'\'';	
		
		// add the client-provided fields	
		foreach ($logData as $dbCol => $dbVal) {
			isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
			isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';
			$dbColList .= $dbCol;
			if (empty($dbVal) && (strlen($dbVal)==0)) {
				$dbValList .= 'NULL';
			} else {
				$dbValList .= '\''.$dbVal.'\'';
			}							
		}
		// everything goes into the transition log
		$queryString = 'INSERT INTO '.$DB_TABLE_TRANSITION_LOG.' ('.$dbColList.') VALUES ('.$dbValList.')';
		$qResult = mysqli_query($link, $queryString);
//			$respDbg['globals'] = $GLOBALS;
		$respDbg['table'] = $logTable;
		$respDbg['queryString'] = $queryString;
		$respDbg['argData'] = $logData;
		$respDbg['columns'] = $dbColList;
		$respDbg['values'] = $dbValList;
		$response['debug'] = $respDbg;
		if (!$qResult) {
			// SQL ERROR
			$respData['sqlQuery'] = $query_string;
			$respData['result'] = 'Error logging data to OPEN log';
			$respData['sqlError'] =  mysqli_sqlstate($link);
			$respData['message'] = mysqli_error($link);
			$response['error'] = $respData;			
		} else {
			// success
			$respData['result'] = $qResult;
			$respData['message'] = 'Log record added to '.$logType.' log';
			$response['data'] = $respData;
		}
	} else {
		// no data base table name.
		// ** This could probably be factored out.
		$errData['message'] = 'Log database table name is empty.';
		$errData['postData'] = $postData;
		$errData['getData'] = $_GET;
		//$errData['globals'] = $GLOBALS;
		$response['error'] = $errData;
	}
	return $response;
}
?>