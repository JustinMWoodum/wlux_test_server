<?php 
require 'config_files.php';

$DB_SERVER = 'localhost';
$response = '';
$badParam = null;

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
		$action = 'config';
		if (!empty($postData[$action])) {			
			$logData = $postData[$action];
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
			
			if (empty($badParam)) {
				// no parameter errors, so get task configuration record
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
					$localErr['message'] = 'Bad parameter in config request.';
					$localErr['paramError']['taskId'] = "Cannot be 0";
					$localErr['request'] = $logData;
					// $errData['globals'] = $GLOBALS;
					$errData['validation'] = $localErr;
				}
				if (!empty($query)) {
					$result = mysqli_query ($link, $query);
					if (mysqli_num_rows($result) == 1 ) {
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
				//bad parameter
				$localErr = '';
				$localErr['message'] = 'Bad parameter in start request.';
				$localErr['paramError'] = $badParam;
				$localErr['request'] = $logData;
				// $errData['globals'] = $GLOBALS;
				$errData['validation'] = $localErr;
			}
		} else {
			// unrecgnized command
			$localErr = '';
			$localErr['message'] = 'Unrecognized command. Command must be "config" .';
			$localErr['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$errData['command'] = $localErr;
		}
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
					
					// save the finish time in case we need it to close the task later
					$finishTime = time();
					$studySessionRecord = null;
					// get the current or most recent task, which is the last task entry for this session
					//  it could be finished (endTime != NULL) or not.
					$query = 'SELECT * FROM '.$DB_TABLE_SESSION_LOG.
						' WHERE sessionId = '.$logData['sessionId'].
							//' AND endTime IS NULL'.
							' ORDER BY startTime DESC LIMIT 1';
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
				
					// +++++
					//    Get all the config records for studyId and conditionId ordered by taskId DESC
					//    iterate through them and:
					//      from the first record, get the max task info
					//      save all the task config records to use later
					// -----
					// get the number of tasks for this study
					if (!empty($studySessionRecord)) {
						$query = 'SELECT * FROM '.$DB_TABLE_STUDY_CONFIG.
							' WHERE studyId = '.$studySessionRecord['studyId'].
								' AND conditionId = '.$studySessionRecord['conditionId'].
							' ORDER BY taskId DESC';
						$result = mysqli_query ($link, $query);
						$studyConfigRecords = array();
						if (mysqli_num_rows($result) > 0 ) {
							$maxTask = 0;
							while ($thisRecord = mysqli_fetch_assoc($result)) {
								// from the first record, get the max task info
								if (empty($maxTask)) {
									$maxTask = $thisRecord['taskId'];
								}
								// save all the config records
								$studyConfigRecords[$thisRecord['taskId']] = $thisRecord;
							}
							if (count($studyConfigRecords) != $maxTask) {							
								$localErr = '';
								$localErr['configRecordCount'] = count($studyConfigRecords);
								$localErr['maxTask'] =  $maxTask;
								$localErr['message'] = 'Task config record count mismatch. The tasks must be numbered in sequence starting with 1.';
								$errData['taskConfig'] = $localErr;
							}
							// $response['debug']['studyConfig']['count'] = mysqli_num_rows($result);
						} else {
							// no records
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Error reading condition count record';
							$localErr['dataRecord'] = $thisRecord;
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['query2data'] = $localErr;
						}
					} else {
						// unable to find session
					}
					//$response['debug']['rawData']['numTasks'] = $;
					//$response['debug']['rawData']['currentTask'] = $currentTask;
					
					// here we have the current task and the current session info
					//  if there is no task, start the first one
					//  if there's an open task, finish it and start the next one
					
					$closeLast = false;
					if (!empty($studyConfigRecords) && !empty($studySessionRecord)) {
						// see if the current task is the last one for the study
						if ($currentTask >= $maxTask) {
							$closeLast = true;
							$closeTask = $currentTask;
							$errData['lastTask'] = 'Task '.$currentTask.' is the last task in this study.';	
						} else {
							// close the last task if it was open and this is not the first task of the study session
							if ((is_null($studySessionRecord['endTime'])) && ($currentTask != 0)) {
								$closeLast = true;
								$closeTask = $currentTask;
							}
							// advance to next task
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
									$escapedString = str_replace("'","''",$dbVal);
									$dbValList .= '\''.$escapedString.'\'';
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
								$sessionBuff['startPageHtml'] = "";
								$sessionBuff['startPageNextUrl'] = "";
								$response['data'] = $sessionBuff;
							}
							
							// if new task started, create corresponding session config record
							
							if ($qResult) {	
								// create a new session_cofig record for this session
								if (!empty($studyConfigRecords)) {
									$studyTaskConfig = $studyConfigRecords[$currentTask];
									$studyTaskConfig['recordSeq'] = NULL;
									$studyTaskConfig['sessionId'] = $newTaskRecord['sessionId'];
	
									// add server-generated fields to insert query
									$dbColList = 'autoConditionId';
									$dbValList = '0';
																	
									// add the client-provided fields	
									foreach ($studyTaskConfig as $dbCol => $dbVal) {
										isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
										isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';										
										$dbColList .= $dbCol;
										if (empty($dbVal) && (strlen($dbVal)==0)) {
											$dbValList .= 'NULL';
										} else {
											$escapedString = str_replace("'","''",$dbVal);
											$dbValList .= '\''.$escapedString.'\'';
										}							
									}
									$queryString = 'INSERT INTO '.$DB_TABLE_SESSION_CONFIG.' ('.$dbColList.') VALUES ('.$dbValList.')';
									$qResult = mysqli_query($link, $queryString);
									if (!$qResult) {
										// SQL ERROR
										$localErr = '';
										$localErr['sqlQuery'] = $queryString;
										$localErr['result'] = 'Error creating session_config record';
										$localErr['sqlError'] =  mysqli_sqlstate($link);
										$localErr['message'] = mysqli_error($link);
										$errData['insert1'] = $localErr;
									} else {
										// finish start response buffer
										$response['data']['startPageHtml'] = $studyTaskConfig['startPageHtml'];
										$response['data']['startPageNextUrl'] = $studyTaskConfig['startPageNextUrl'];
										$response['data']['finishPageNextUrl'] = $studyTaskConfig['finishPageNextUrl'];
										$response['data']['taskType'] = $studyTaskConfig['taskType'];
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
						} 
					} else {
						// record not found
						$localErr = '';
						$localErr['message'] = 'Config or session log record not found';
						$localErr['configRecord'] = (!empty($studyConfigRecords)) ? $studyConfigRecords : null ;
						$localErr['sessionRecord'] = (!empty($studySessionRecord)) ? $studySessionRecord : null ;
						$errData['configOrLog'] = $localErr;
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
								 ' AND taskId = '.$closeTask;
						$result = mysqli_query ($link, $query);
						// $response['debug']['query'] = $query;
						// $response['debug']['result'] = $result;						
						if ($result) {
							$localErr = '';
							$localErr['finishTime'] = $finishTimeText;
							$localErr['taskId'] = $closeTask;
							$localErr['message'] = 'The previous task was not finished.';
							$errData['openTask'] = $localErr;
						} else {
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Error finishing session_log entry';
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['update1'] = $localErr;
						}			
					}
				} else {
					//bad parameter
					$closeLast = false;
					$localErr = '';
					$localErr['message'] = 'Bad parameter in start request.';
					$localErr['paramError'] = $badParam;
					$localErr['request'] = $logData;
					// $errData['globals'] = $GLOBALS;
					$errData['validation'] = $localErr;
				} 
				// something happened up there, but it was already reported
			} else {
				// see if this is a finish request
				$action = 'finish';								
				if (!empty($postData[$action])) {
					//$response['debug'] = $postData;
					$logData = $postData[$action];
					// TODO: Need to test the parameters in the request
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
									 ' AND endTime = NULL '.
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

$thisParam = "callback";
if (array_key_exists($thisParam, $_GET)) {
	$jsonpTag = $_GET[$thisParam]; // set by jquery ajax call when using jsonp data type
}

if (!empty($jsonpTag)) { 
	// format and send output
	// no error information is returned in the JSONP response!
	$fnResponse = $jsonpTag . '(' . json_encode($response['data']) . ')';
} else {
	// no callback param name so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$fnResponse = json_encode($response);
} 
print ($fnResponse);
?>