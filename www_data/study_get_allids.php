<?php
function _study_get_allids ($link, $logData) {
	require 'config_files.php';
	//test request type
	if ($logData['studyId'] == '*') {
		// return the specified configuration
		$query = 'SELECT DISTINCT studyId FROM '.$DB_TABLE_STUDY_CONFIG;
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
		if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
			$badParam[$thisParam] = "Missing or not a number";
		} else {
			$studyId = $logData[$thisParam];
		}
		
		if(empty($badParam)) {										
	
			// return the specified configuration
			$query = 'SELECT DISTINCT taskId, conditionId FROM '.$DB_TABLE_STUDY_CONFIG. 
					' WHERE studyId = '.$studyId;
			$result = mysqli_query ($link, $query);
			$lastTaskId = -1;
			$conditionIdCount = 0;
			if (mysqli_num_rows($result) > 0) {
				$response['data']['studyId'] = $studyId;
				$response['data']['conditionCount'] = 0;
				$response['data']['conditionsBalanced'] = true;
				$response['data']['count'] = 0;
				while ($thisRecord = mysqli_fetch_assoc($result)) {
					if ($lastTaskId != $thisRecord['taskId']) {
						// set up for a new task
						$lastTaskId = $thisRecord['taskId'];
						$conditionIdCount = 0;
						$response['data']['count'] = $response['data']['count'] + 1;
					}
					$response['data']['tasks'][$thisRecord['taskId']][$conditionIdCount] = $thisRecord['conditionId'];
					$conditionIdCount = $conditionIdCount  + 1;
				}
				// test task and condition symmetry: each task should have the same conditions
				// look for a difference. They shoud all be the same length
				$lastConditionCount = -1;
				foreach ($response['data']['tasks'] as $thisTask) {
					if ($lastConditionCount == -1) {
						$lastConditionCount = count($thisTask);
					} else if ($lastConditionCount != count($thisTask)) {
						$response['data']['conditionsBalanced'] = false;
					}
				}
				if ($response['data']['conditionsBalanced'] == true) {
					$response['data']['conditionCount'] = $lastConditionCount;
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