<?php
function _task_post_start ($link, $logData) {
	require 'config_files.php';
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
		
	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;	
}
?>