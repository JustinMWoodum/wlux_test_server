<?php 
require 'config_files.php';

$DB_SERVER = 'localhost';
$response = '';

// get the request data
$postData = json_decode($HTTP_RAW_POST_DATA,true);

// if the data is not in the raw post data, try the post form
if (empty($postData)) {
	$postData = $_POST;
}

// if the data is not in the the post form, try the query string		
if (empty($postData)) {
	$postData = $_GET;
} 

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
	// connected to database, check for a get request
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		// see if this is a finish request
		$action = 'config';
		$logData = $postData[$action];
		if (!empty($logData)) {
			// return the specified configuration
			
			// check the parameters
			$thisParam = 'sessionId';
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
				$query = 'SELECT * FROM '.$DB_TABLE_SESSION_CONFIG.' WHERE sessionId = '.$logData['sessionId'].
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
		} else {						
			// unrecognized command
			$errData['message'] = 'Action is not recognized. Action must be \'config\', \'start\', or \'finish\'';
			$errData['postData'] = $postData;
			$errData['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$response['error'] = $errData;
		}
	} else {
		// check for a POST request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {			
			// determine log type from variable name
	//		$respDbg['globals'] = $GLOBALS;
			$respDbg['argData'] = $postData;
			$response['debug'] = $respDbg;	
	
			$action = 'start';
			$logData = $postData[$action];

			if (!empty($logData)) {
				// start a new task and return a start response
				// get the number of conditions to pick from
				$numConditions = 0;
				$query = "SELECT COUNT(studyId) AS conditionCount FROM ".$DB_TABLE_STUDY_CONFIG." WHERE studyId = ".$logData['studyId']; 
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

/*
					if ($result) {				
						// read conifguration for this study and condition and task
						$query = 'SELECT * FROM '.$DB_TABLE_STUDY_CONFIG.
							' WHERE studyId = '.$logData['studyId'].' AND taskId = '.$thisTask.' AND conditionId = '.$thisCondtion; 				
						$result = mysqli_query ($link, $query);
						if (mysqli_num_rows($result) == 1) {
							if ($thisRecord = mysqli_fetch_assoc($result))  {
								$thisStudySession = $thisRecord;
							} else {
								$localErr = '';
								$localErr['sqlQuery'] = $query;
								$localErr['result'] = 'Error reading new session log record';
								$localErr['dataRecord'] = $thisRecord;
								$localErr['sqlError'] =  mysqli_sqlstate($link);
								$localErr['message'] = mysqli_error($link);
								$errData['query2data'] = $localErr;
							}
						} else {
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Reading study config for this condition returned '.mysqli_num_rows($result). ' records';
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['query2'] = $localErr;
						}
						
						// create a new session_cofig record for this session
						if (!empty($thisStudySession)) {
							$thisStudySession['recordSeq'] = "";
							$thisStudySession['sessionId'] = $sessionId;
							$thisStudySession['taskId'] = 1;
							// add server-generated fields to insert query
							$dbColList = 'autoConditionId';
							$dbValList = '0';	
							
							// add the client-provided fields	
							foreach ($thisStudySession as $dbCol => $dbVal) {
								isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
								isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';
								$dbColList .= $dbCol;
								$dbValList .= '\''.$dbVal.'\'';
							}
							$queryString = 'INSERT INTO '.$DB_TABLE_SESSION_CONFIG.' ('.$dbColList.') VALUES ('.$dbValList.')';
							$qResult = mysqli_query($link, $queryString);
							if (!$qResult) {
								// SQL ERROR
								$localErr = '';
								$localErr['sqlQuery'] = $query_string;
								$localErr['result'] = 'Error creating session_config record';
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
						$localErr = '';
						$localErr['sqlQuery'] = $query;
						$localErr['result'] = 'Error creating session_log entry';
						$localErr['sqlError'] =  mysqli_sqlstate($link);
						$localErr['message'] = mysqli_error($link);
						$errData['update1'] = $localErr;
					}			
*/				}
			} else {
				// see if this is a finish request
				$action = 'finish';
				$logData = $postData[$action];
				$response['debug'] = $postData;
				if (!empty($logData)) {
					// finish the session specified in the request
					$finishTime = time();
					$finishTimeText = date('Y-m-d H:i:s', $finishTime);
					// TODO: Need to check to see if this has been closed, already.
					//   if so, return an error, otherwise, update the record.
					
					// close the task 0 record for this session
					$query = 'UPDATE '.$DB_TABLE_SESSION_LOG.
							 ' SET endTime = "'.$finishTimeText.
							 '" WHERE sessionId = '.$logData['sessionId'].
							 ' AND taskId = 0';
					$result = mysqli_query ($link, $query);
					// $response['debug']['query'] = $query;
					// $response['debug']['result'] = $result;						
					if ($result) {
						$rData = array();
						$response['data'] = $rData;			
					} else {
						$localErr = '';
						$localErr['sqlQuery'] = $query;
						$localErr['result'] = 'Error finishing session_log entry';
						$localErr['sqlError'] =  mysqli_sqlstate($link);
						$localErr['message'] = mysqli_error($link);
						$errData['update1'] = $localErr;
					}			
				} else {
					// unrecognized command
					$localErr = '';
					$localErr['message'] = 'Action is not recognized. Action must be \'config\', \'start\', or \'finish\'';
					$localErr['postData'] = $postData;
					$localErr['getData'] = $_GET;
					// $errData['globals'] = $GLOBALS;
					$errData['command'] = $localErr;
				}
			}
		} else {
			$localErr = '';
			// method not supported
			$localErr['message'] = 'HTTP method not recognized. Method must be \'GET\' or \'POST\'';
			$errData['method'] = $localErr;
		}
	}
	mysqli_close($link);
}

if (!headers_sent()) {
	header('content-type: application/json');
	header('X-PHP-Response-Code: 200', true, 200);
}
if (!empty($errData)) {
	$response['error'] = $errData;
}

print (json_encode($response));
?>