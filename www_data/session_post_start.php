<?php
function _session_post_start ($link, $logData) {
require 'config_files.php';	
	// start a new task and return a start response
	// get the number of conditions to pick from
	$numConditions = 0;
	
	$thisParam = 'studyId';
	if (array_key_exists($thisParam, $logData)) {
		if (!is_numeric($logData[$thisParam])) {
			$badParam[$thisParam] =  "Not a number";
		} else {
			$studyId = $logData[$thisParam];
		}
	} else {
		$badParam[$thisParam] =  "Missing";
	}

	if (empty($badParam)) {	
		$query = "SELECT COUNT(studyId) AS conditionCount FROM ".$DB_TABLE_STUDY_CONFIG." WHERE studyId = ".$studyId. " AND taskId = 1"; 
		$result = mysqli_query ($link, $query);
		if (mysqli_num_rows($result) == 1) {
			if ($thisRecord = mysqli_fetch_assoc($result))  {
				$numConditions = $thisRecord['conditionCount'];
			} else {
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Error reading condition count record';
				$localErr['dataRecord'] = $thisRecord;
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['query1data'] = $localErr;
			}
		} else {
			$localErr = '';
			$localErr['sqlQuery'] = $query;
			$localErr['result'] = 'Error reading condition count';
			$localErr['sqlError'] =  mysqli_sqlstate($link);
			$localErr['message'] = mysqli_error($link);
			$errData['query1'] = $localErr;
		}
		
		if ($numConditions > 0) {
			// query study config for a random condition
			//  **note sessionId will probably come from elsewhere, so
			//  ** we'll just get a timestamp for now to keep it unique
			$thisCondtion =  round(mt_rand(1,$numConditions),0,PHP_ROUND_HALF_UP);
			$sessionId = time();
			$startTimeText = date('Y-m-d H:i:s', $sessionId);
			$thisStudySession = NULL;
			$thisTask = 0; //  the first task a session starts with is task 0
			// create a new session_log record 
			$query = 'INSERT INTO '.$DB_TABLE_SESSION_LOG.' (recordSeq, studyId, sessionId, taskId, conditionId, startTime, endTime) VALUES '. 
				'(NULL, \''.$logData['studyId'].'\', \''.$sessionId.'\', \''.$thisTask.'\', \''.$thisCondtion.'\', \''.$startTimeText.'\', NULL)';
			$result = mysqli_query ($link, $query);
			if (!$result) {
				// SQL ERROR
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Error creating new session_log record';
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['insert1'] = $localErr;
			} else {
				// format start response buffer
				$sessionBuff['studyId'] = $logData['studyId'];
				$sessionBuff['sessionId'] = $sessionId;
				$sessionBuff['conditionId'] = $thisCondtion;
				$sessionBuff['startTime'] = $startTimeText;
				$response['data'] = $sessionBuff;
			}						
		}
	} else {
		// a bad parameter was passed
		$localErr = '';
		$localErr['message'] = 'Bad parameter in request.';
		$localErr['paramError'] = $badParam;
		$localErr['request'] = $logData;
		// $localErr['globals'] = $GLOBALS;
		$errData['validation'] = $localErr;		
	}
	
	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;
}
?>