<?php
function log_get_allids ($link, $logData) {
require 'config_files.php';
    $response['debug']['logData'] = $logData;
	if ($logData['studyId'] == '*') {
		// return the specified configuration
		$query = 'SELECT DISTINCT s.studyId FROM '.$DB_TABLE_SESSION_CONFIG.
					' AS s JOIN '.$DB_TABLE_TRANSITION_LOG.'  AS l'.
					' WHERE l.sessionId = s.sessionId ORDER BY l.sessionId';		
		$result = mysqli_query ($link, $query);
		$recordIndex = 0;
		$response['data']['count'] = mysqli_num_rows($result);
		if ($response['data']['count'] > 0) {
			while ($thisRecord = mysqli_fetch_assoc($result)) {
				$response['data']['studyIds'][$recordIndex] = $thisRecord['studyId'];
				$recordIndex = $recordIndex + 1;
			}
		} else {
			$localErr = '';
			$localErr['sqlQuery'] = $query;
			$localErr['result'] = 'Reading study config returned '.mysqli_num_rows($result). ' records';
			$localErr['sqlError'] =  mysqli_sqlstate($link);
			$localErr['message'] = mysqli_error($link);
			$errData['query'] = $localErr;
			$response['error'] = $errData;		
		}
	} else {
		//get the details for a specific study
		// check the parameters
		$thisParam = 'studyId';
		if (empty($logData[$thisParam] ) || !is_numeric($logData[$thisParam] )) {
			$badParam[$thisParam] = "Missing or not a number";
		} else {
			$studyId = $logData[$thisParam] ;
		}
		
		if(empty($badParam)) {										
			// return the specified configuration
			$query = 'SELECT DISTINCT s.sessionId, l.taskId FROM '.$DB_TABLE_SESSION_CONFIG.
					' AS s JOIN '.$DB_TABLE_TRANSITION_LOG.'  AS l'.
					' WHERE l.sessionId = s.sessionId AND s.studyId = '.$studyId.' ORDER BY l.sessionId, l.taskId';		
			$result = mysqli_query ($link, $query);
			if (mysqli_num_rows($result) > 0) {
				$response['data']['studyId'] = $studyId;
				$response['data']['count'] = 0;
				while ($thisRecord = mysqli_fetch_assoc($result)) {
					$response['data']['sessionIds'][$thisRecord['sessionId']][] = $thisRecord['taskId'];
				}
				$response['data']['count'] = count ($response['data']['sessionIds']);
			} else {
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Reading study config returned '.mysqli_num_rows($result). ' records';
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['query'] = $localErr;
				$response['error'] = $errData;
			}
		} else {
			// bad parameter in request data
			$errData['message'] = 'Bad parameter in request.';
			$errData['paramError'] = $badParam;
			$errData['request'] = $logData;
			// $errData['globals'] = $GLOBALS;
			$response['error'] = $errData;		
		}
	}
	return $response;
}
?>