<?php

function _session_get($link, $postData) {
require 'config_files.php';	
	// see if this is a finish request
	$action = 'config';
	if (!empty($postData[$action])) {
		$logData = $postData[$action];
		// return the specified configuration
		
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
			// a missing taskId is OK.
			// we'll just all tasks
			// TODO: one of these days
			$taskId = 0;
		}
		
		if(empty($badParam)) {
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
					}
				} else {
					$localErr = '';
					$localErr['sqlQuery'] = $query;
					$localErr['result'] = 'Reading study config returned '.mysqli_num_rows($result). ' records';
					$localErr['sqlError'] =  mysqli_sqlstate($link);
					$localErr['message'] = mysqli_error($link);
					$errData['query'] = $localErr;
				}
			}
		} else {
			// bad parameter in request data
			$localErr = '';
			$localErr['message'] = 'Bad parameter in request.';
			$localErr['paramError'] = $badParam;
			$localErr['request'] = $logData;
			// $errData['globals'] = $GLOBALS;
			$errData['validation'] =$localErr;		
		}
	} else {
		$action = 'log';
		if (!empty($postData[$action])) {
			$logData = $postData[$action];
			// return the task info from the log
			
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
		} else {			
			// unrecognized command
			$localErr = '';
			$localErr['message'] = 'Action is not recognized. Action must be \'config\', or \'log\'';
			$localErr['postData'] = $postData;
			$localErr['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$errData['command'] = $localErr;
		}
	}
	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;

}


?>