<?php

function _log_get ($link, $postData) {
require 'config_files.php';
	// query the database for the requested info
	// check the parameters
			
	$thisParam = 'sessionId';
	$sessionId = 0;
	if (array_key_exists($thisParam, $postData)) {
		$sessionId = trim($postData[$thisParam]);
		if (!is_numeric($sessionId)) {
			$badParam[$thisParam] =  "Not a number";
		}
	} else {
		$badParam[$thisParam] =  "Missing";
	}		

	$thisParam = 'taskId';
	$taskId = 0;
	if (array_key_exists($thisParam, $postData)) {
		$taskId = trim($postData[$thisParam]);
		if (!is_numeric($taskId)) {
			$badParam[$thisParam] =  "Not a number";
		}
	} else {
		// Task ID == 0 == All tasks in the session
		$taskId = 0;
	}				
//+
	if (empty($badParam)) {
		// no parameter errors, so get task configuration record
		// first get the open records
		$response['debug']['sessionId'] = $sessionId ;
		$response['debug']['taskId'] = $taskId ;
		
		if ($taskId > 0) {	
			$query = 'SELECT * FROM '.$DB_TABLE_TRANSITION_LOG.
				' WHERE taskId = '.$taskId. 
				' AND sessionId = '.$sessionId.
				' ORDER BY serverTimestamp ;';
		} else {
			// get all tasks for this session
			$query = 'SELECT * FROM '.$DB_TABLE_TRANSITION_LOG.
				' WHERE sessionId = '.$sessionId.
				' ORDER BY serverTimestamp ;';				
		}
		
		$response['debug']['query'] = $query;
		
		$result = mysqli_query ($link, $query);
		if ($result) {
			$openLogRecords = array();
			$openLogRecordCount = 0;
			while ($thisRecord = mysqli_fetch_assoc($result)) {
				unset($thisRecord['recordSeq']);
				if ($taskId > 0) {						
					$openLogRecords[$openLogRecordCount] = array_merge($thisRecord);
					$openLogRecordCount = $openLogRecordCount + 1;
				} else {
					$thisTask = $thisRecord['taskId'];
					if (empty($openLogRecords[$thisTask])) {
						$openLogRecords[$thisTask] = array();
					}
					array_push($openLogRecords[$thisTask], array_merge($thisRecord));
				}
			}
			$response['data'] = $openLogRecords;
		} else {
			// query error
			$respData['sqlQuery'] = $query;
			$respData['result'] = 'Error logging data to OPEN log';
			$respData['sqlError'] =  mysqli_sqlstate($link);
			$respData['message'] = mysqli_error($link);
			$response['error'] = $respData;		
		}
	} else {
		// bad or missing parameter
		$localErr = '';
		$localErr['message'] = 'Bad parameter in finish request.';
		$localErr['paramError'] = $badParam;
		$localErr['request'] = $postData;
		// $errData['globals'] = $GLOBALS;
		$errData['validation'] = $localErr;
	}
//-
	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;
}
?>
