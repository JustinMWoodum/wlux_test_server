<?php

function _task_post_finish ($link, $logData) {
	require 'config_files.php';
	// TODO: Need to test the parameters in the request to make sure
	//   they identify a valid task to finish
	
	// check the parameters
	$thisParam = 'sessionId';
	if (array_key_exists($thisParam, $logData)) {
		if (!is_numeric($logData[$thisParam])) {
			$badParam[$thisParam] =  "Not a number";
		} else {
			$sessionId = $logData[$thisParam];
		}
	} else {
		$badParam[$thisParam] =  "Missing";
	}

	$thisParam = 'taskId';
	if (array_key_exists($thisParam, $logData)) {
		if (!is_numeric($logData[$thisParam])) {
			$badParam[$thisParam] =  "Not a number";
		} else {
			$taskId = $logData[$thisParam];
		}
	} else {
		$taskId = 0;
	}

	if (empty($badParam)) {						
		// get the config record for this task & session
		
		if ($taskId == -1) {
			// -1 ==> get the latest task config for this session
			$query = 'SELECT * FROM '.$DB_TABLE_SESSION_CONFIG.
				' WHERE sessionId = '.$sessionId.
				' ORDER BY taskId DESC LIMIT 1';
		} else if ($taskId > 0) {
			// get the config record by session and task
			$query = 'SELECT * FROM '.$DB_TABLE_SESSION_CONFIG.
				' WHERE sessionId = '.$sessionId.
				' AND taskId = '.$taskId;
		} else {
			// task == 0 is not supported yet
			// bad parameter
			$localErr = '';
			$localErr['message'] = 'Bad parameter in finish request.';
			$localErr['paramError']['taskId'] = "Cannot be 0";
			$localErr['request'] = $logData;
			// $errData['globals'] = $GLOBALS;
			$errData['validation'] = $localErr;
		}
		if (!empty($query)) {
			$result = mysqli_query ($link, $query);
			if (mysqli_num_rows($result) == 1 ) {
				if ($thisTaskRecord = mysqli_fetch_assoc($result)) {
					if ($taskId <= 0) {
						// set the current task
						$taskId = $thisTaskRecord['taskId'];
						// continue
					}
				} else {
					$thisTaskRecord = null;
				}
			} else {
				// no records
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Error reading session configuration';
				$localErr['dataRecord'] = $thisRecord;
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['queryConfig'] = $localErr;
			}
			
			// finish the session specified in the request
			$finishTime = time();
			$finishTimeText = date('Y-m-d H:i:s', $finishTime);
			// TODO: Need to check to see if this has been closed, already.
			//   if so, return an error, otherwise, update the record.
			//   for now, we'll just only update unfinished records.
			$query = 'UPDATE '.$DB_TABLE_SESSION_LOG.
					 ' SET endTime = "'.$finishTimeText.
					 '" WHERE sessionId = '.$sessionId.
					 ' AND endTime IS NULL '.
					 ' AND taskId = '.$taskId;
			$result = mysqli_query ($link, $query);
			// $response['debug']['query'] = $query;
			// $response['debug']['result'] = $result;						
			if ($result) {
				$rData = array();
				$rData['taskId'] = $taskId;
				$rData['finishTime'] = $finishTimeText;
				$rData['finishPageHtml'] = $thisTaskRecord['finishPageHtml'];
				$rData['finishPageNextUrl'] = $thisTaskRecord['finishPageNextUrl'];
				$response['data'] = $rData;		
			} else {
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Error finishing session_log entry';
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['updateTime'] = $localErr;
			}
		}
	} else {
		// bad parameter
		$localErr = '';
		$localErr['message'] = 'Bad parameter in finish request.';
		$localErr['paramError'] = $badParam;
		$localErr['request'] = $logData;
		// $errData['globals'] = $GLOBALS;
		$errData['validation'] = $localErr;
	}

	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;	
}
?>