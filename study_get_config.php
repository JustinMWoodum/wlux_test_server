<?php
function _study_get_config ($link, $logData) {			
	require 'config_files.php';
	// return the specified configuration
	
	// check the parameters
	$thisParam = 'studyId';
	if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
		$badParam[$thisParam] = "Missing or not a number";
	}

	$thisParam = 'conditionId';
	if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
		$badParam[$thisParam] = "Missing or not a number";
	}
	
	$thisParam = 'taskId';
	if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
		$badParam[$thisParam] = "Missing or not a number";
	}
	
	if(empty($badParam)) {										
		// read conifguration for this study and condition
		$query = 'SELECT * FROM '.$DB_TABLE_STUDY_CONFIG.' WHERE studyId = '.$logData['studyId'].
//		$query = 'SELECT * FROM '.'study_config'.' WHERE studyId = '.$logData['studyId'].
			' AND conditionId = '.$logData['conditionId'].
			' AND taskId = '.$logData['taskId'];							 	
		$result = mysqli_query ($link, $query);
		if (mysqli_num_rows($result) == 1) {
			if ($thisRecord = mysqli_fetch_assoc($result))  {
				// remove the recordSeq field
				unset($thisRecord['recordSeq']);
				$response['data'] = array_merge($thisRecord);
				foreach ($response['data'] as $k => $v) {
					// set "null" strings to null values
					if ($v == 'NULL') {
						$response['data'][$k] = NULL;
					}
				}
			} else {
				$localErr = '';
				$localErr['sqlQuery'] = $query;
				$localErr['result'] = 'Error reading config query';
				$localErr['dataRecord'] = $thisRecord;
				$localErr['sqlError'] =  mysqli_sqlstate($link);
				$localErr['message'] = mysqli_error($link);
				$errData['queryData'] = $localErr;
				$response['error'] = $errData;		
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
	return ($response);
}
?>