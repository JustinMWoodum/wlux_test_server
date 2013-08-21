<?php 
require 'config_files.php';

$DB_SERVER = 'localhost';
$response = '';

// get the request data
if (!empty($HTTP_RAW_POST_DATA)) {
	$postData = json_decode($HTTP_RAW_POST_DATA,true);
}

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
	$localErr = '';
	$localErr['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$errData['dbconnect'] = $localErr;
} else {
	// connected to database, check for a get request
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		// unrecognized command
		$localErr = '';
		$localErr['message'] = 'GET Method is not supported.';
		$localErr['postData'] = $postData;
		$localErr['getData'] = $_GET;
		// $errData['globals'] = $GLOBALS;
		$errData['command'] = $localErr;
	} else {
		// check for a POST request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {			
			// determine log type from variable name
	//		$respDbg['globals'] = $GLOBALS;
			$respDbg['argData'] = $postData;
			$response['debug'] = $respDbg;	
	
			$action = 'start';
			if (!empty($postData[$action])) {			
				$logData = $postData[$action];
				// check the parameters
				$thisParam = 'sessionId';
				if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
					$badParam[$thisParam] = "Missing or not a number";
				}
				
				if (empty($badParam)) {
					// no parameter errors, so start the task 
			
					// get the current task
					$query = 'SELECT * FROM '.$DB_TABLE_SESSION_LOG.
						' WHERE sessionId = '.$logData['sessionId'].
							' AND endTime IS NULL'.
							' ORDER BY startTime DESC LIMIT 1';
					// currentTask = taskId
					$result = mysqli_query ($link, $query);
					if (mysqli_num_rows($result) > 0) {
						if ($thisRecord = mysqli_fetch_assoc($result))  {
							$studySessionRecord = $thisRecord;
							$currentTask = $thisRecord['taskId'];
							//$response['debug']['logData'] = $studySessionRecord;
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
	
					// get the number of tasks for this study
					$query = 'SELECT * FROM '.$DB_TABLE_STUDY_CONFIG.
						' WHERE studyId = '.$studySessionRecord['studyId'].
							' AND conditionId = '.$studySessionRecord['conditionId'].
						' ORDER BY TaskId DESC LIMIT 1';
					// currentTask = taskId
					$result = mysqli_query ($link, $query);
					if (mysqli_num_rows($result) > 0 ) {
						if ($thisRecord = mysqli_fetch_assoc($result))  {
							$studyConfigRecord = $thisRecord;
							$maxTask = $thisRecord['taskId'];
							// $response['debug']['configData'] = $studyConfigRecord;
						} else {
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Error reading condition count record';
							$localErr['dataRecord'] = $thisRecord;
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['query2data'] = $localErr;
						}
					} else {
						$localErr = '';
						$localErr['sqlQuery'] = $query;
						$localErr['result'] = 'Error reading condition count';
						$localErr['sqlError'] =  mysqli_sqlstate($link);
						$localErr['message'] = mysqli_error($link);
						$errData['query2'] = $localErr;
					}
					
					//$response['debug']['rawData']['numTasks'] = $maxTask;
					//$response['debug']['rawData']['currentTask'] = $currentTask;
					
					// here we have the current task and the current session info
					//  if there is no task, start the first one
					//  if there's an open task, finish it and start the next one
					
					$closeLast = false;
					if (!empty($studyConfigRecord) && !empty($studySessionRecord)) {
						if ($currentTask >= $maxTask) {
							$closeLast = true;
							$finishTime = time();
							$errData['lastTask'] = 'Task '.$currentTask.' is the last task in this study.';	
						} else {
							$currentTask = $currentTask + 1;
							// start a new task and return a start response
							$newTaskRecord = array();
							$newTaskRecord['recordSeq'] = NULL;
							$newTaskRecord['studyId'] = $studySessionRecord['studyId'];
							$newTaskRecord['sessionId'] = $studySessionRecord['sessionId'];
							$newTaskRecord['taskId'] = $currentTask;
							$newTaskRecord['conditionId'] = $studySessionRecord['conditionId'];
							$taskStartTime = time();
							$newTaskRecord['startTime'] = date('Y-m-d H:i:s', $taskStartTime);
	
							//$response['debug']['newTask'] = $newTaskRecord;
							
							foreach ($newTaskRecord as $dbCol => $dbVal) {
								isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
								isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';
								$dbColList .= $dbCol;
								if (empty($dbVal) && (strlen($dbVal)==0)) {
									$dbValList .= 'NULL';
								} else {
									$dbValList .= '\''.$dbVal.'\'';
								}							
							}
							$queryString = 'INSERT INTO '.$DB_TABLE_SESSION_LOG.' ('.$dbColList.') VALUES ('.$dbValList.')';
							$qResult = mysqli_query($link, $queryString);
							if (!$qResult) {
								// SQL ERROR
								$localErr['sqlQuery'] = $queryString;
								$localErr['result'] = 'Error creating new task record in session_log';
								$localErr['sqlError'] =  mysqli_sqlstate($link);
								$localErr['message'] = mysqli_error($link);
								$errData['writeError'] = $localErr;			
							} else {
								// format start response buffer
								$sessionBuff['studyId'] = $newTaskRecord['studyId'];
								$sessionBuff['sessionId'] = $newTaskRecord['sessionId'];
								$sessionBuff['taskId'] = $newTaskRecord['taskId'];
								$sessionBuff['conditionId'] = $newTaskRecord['conditionId'];
								$sessionBuff['startTime'] = $newTaskRecord['startTime'];
								$response['data'] = $sessionBuff;
							}
							
							// if new task started, create corresponding session config record
							
							if ($qResult) {	
								// create a new session_cofig record for this session
								if (!empty($studyConfigRecord)) {
									$studyConfigRecord['recordSeq'] = NULL;
									$studyConfigRecord['sessionId'] = $newTaskRecord['sessionId'];
	
									// add server-generated fields to insert query
									$dbColList = 'autoConditionId';
									$dbValList = '0';
																	
									// add the client-provided fields	
									foreach ($studyConfigRecord as $dbCol => $dbVal) {
										isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
										isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';
										$dbColList .= $dbCol;
										if (empty($dbVal) && (strlen($dbVal)==0)) {
											$dbValList .= 'NULL';
										} else {
											$dbValList .= '\''.$dbVal.'\'';
										}							
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
									} /* else {
										// format start response buffer
										$sessionBuff['studyId'] = $logData['studyId'];
										$sessionBuff['sessionId'] = $sessionId;
										$sessionBuff['conditionId'] = $thisCondtion;
										$sessionBuff['startTime'] = $startTimeText;
										$response['data'] = $sessionBuff;
									} */						
								}
							} else {
								$localErr = '';
								$localErr['sqlQuery'] = $query;
								$localErr['result'] = 'Error creating session_log entry';
								$localErr['sqlError'] =  mysqli_sqlstate($link);
								$localErr['message'] = mysqli_error($link);
								$errData['update1'] = $localErr;
							}								
						} 
					} else {
						//bad parameter 
						$closeLast = false;
						$localErr = '';
						$localErr['message'] = 'Bad parameter in request.';
						$localErr['paramError'] = $badParam;
						$localErr['request'] = $logData;
						// $errData['globals'] = $GLOBALS;
						$errData['validation'] = $localErr;
					}
					
					if ($closeLast) {
						// else if doing last task,
						//   close last task, if open
						// finish the session specified in the request
						$finishTimeText = date('Y-m-d H:i:s', $finishTime);
						// TODO: Need to check to see if this has been closed, already.
						//   if so, return an error, otherwise, update the record.
						
						// close the task  record for this session
						$query = 'UPDATE '.$DB_TABLE_SESSION_LOG.
								 ' SET endTime = "'.$finishTimeText.
								 '" WHERE sessionId = '.$logData['sessionId'].
								 ' AND taskId = '.$currentTask;
						$result = mysqli_query ($link, $query);
						// $response['debug']['query'] = $query;
						// $response['debug']['result'] = $result;						
						if ($result) {
							$rData = array();
							$errData['openTask'] = 'The previous task was not finished.';
							// no response is necessary
							// $response['data'] = $rData;			
						} else {
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Error finishing session_log entry';
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['update1'] = $localErr;
						}			
					}
				//   return done buffer
				} else {
					// something happened up there, but it was already reported
				}
			} else {
				// see if this is a finish request
				$action = 'finish';								
				if (!empty($postData[$action])) {
					//$response['debug'] = $postData;
					$logData = $postData[$action];
					// TODO: Need to test the parameters in the request
					// check the parameters
					$thisParam = 'sessionId';
					if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
						$badParam[$thisParam] = "Missing or not a number";
					}
	
					$thisParam = 'taskId';
					if (empty($logData[$thisParam]) || !is_numeric($logData[$thisParam])) {
						$badParam[$thisParam] = "Missing or not a number";
					}
			
					if(empty($badParam)) {						
						// finish the session specified in the request
						$finishTime = time();
						$finishTimeText = date('Y-m-d H:i:s', $finishTime);
						// TODO: Need to check to see if this has been closed, already.
						//   if so, return an error, otherwise, update the record.
						
						// close the task 0 record for this session
						$query = 'UPDATE '.$DB_TABLE_SESSION_LOG.
								 ' SET endTime = "'.$finishTimeText.
								 '" WHERE sessionId = '.$logData['sessionId'].
								 ' AND taskId = '.$logData['taskId'];
						$result = mysqli_query ($link, $query);
						// $response['debug']['query'] = $query;
						// $response['debug']['result'] = $result;						
						if ($result) {
							$rData = array();
							$rData['taskId'] = $logData['taskId'];
							$rData['finishTime'] = $finishTimeText;
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
						// bad parameter
						$localErr = '';
						$localErr['message'] = 'Bad parameter in request.';
						$localErr['paramError'] = $badParam;
						$localErr['request'] = $logData;
						// $errData['globals'] = $GLOBALS;
						$errData['validation'] = $localErr;
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
			// method not supported
			$localErr = '';
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