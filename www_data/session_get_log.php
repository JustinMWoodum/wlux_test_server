<?php
function _session_get_log ($link, $logData) {
require 'config_files.php';
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
		$badParam[$thisParam] =  "Missing";
	}
	
	if(empty($badParam)) {										
		// read conifguration for this study and condition
		$query = 'SELECT * FROM '.$DB_TABLE_SESSION_LOG.
			' WHERE sessionId = '.$logData['sessionId'].
			' AND taskId = '.$logData['taskId'];							 	
		$result = mysqli_query ($link, $query);
		if (mysqli_num_rows($result) == 1) {
			//TODO: Add support for taskId = *				
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
			}
		} else {
			$localErr = '';
			$localErr['sqlQuery'] = $query;
			$localErr['result'] = 'Record matching request could not be found.';
			$localErr['sqlError'] =  mysqli_sqlstate($link);
			$localErr['message'] = mysqli_error($link);
			$errData['queryData'] = $localErr;
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