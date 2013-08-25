<?php
require 'config_files.php';
// handles log database requests
$DB_SERVER = 'localhost';

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
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
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// add record to the appropriate log table 
		// determine log type from variable name
		$logType = 'open';
		if (!empty($postData[$logType])) {
			$logData = $postData[$logType];
			// $logData contains an open data block
			$logTable = $DB_TABLE_OPEN_LOG;
			// TODO: Validate fields
		} else {
			// $logData contains an transition data block
			$logType = 'transition';
			if (!empty($postData[$logType])) {
				$logData = $postData[$logType];
				// process transition log request
				$logTable = $DB_TABLE_TRANSITION_LOG;
				// TODO: Validate fields
			} else {
				// unrecognized command
				$errData['message'] = 'Log type not recognized. Log type must be \'open\' or \'transition\'';
				$errData['postData'] = $postData;
				$errData['getData'] = $_GET;
				//$errData['globals'] = $GLOBALS;
				$response['error'] = $errData;
			}
		}
		if (!empty($logTable)) {
			// process the log request
			// make query string from the data structure 
			
			// add server-generated fields to insert query
			$dbColList = 'recordSeq, serverTimestamp';
			$dbValList = 'NULL, CURRENT_TIMESTAMP';	
			
			// add the client-provided fields	
			foreach ($logData as $dbCol => $dbVal) {
				isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
				isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';
				$dbColList .= $dbCol;
				if (empty($dbVal) && (strlen($dbVal)==0)) {
					$dbValList .= 'NULL';
				} else {
					$dbValList .= '\''.$dbVal.'\'';
				}							
			}
			// everything goes into the transition log
			$queryString = 'INSERT INTO '.$DB_TABLE_TRANSITION_LOG.' ('.$dbColList.') VALUES ('.$dbValList.')';
			$qResult = mysqli_query($link, $queryString);
//			$respDbg['globals'] = $GLOBALS;
			$respDbg['table'] = $logTable;
			$respDbg['queryString'] = $queryString;
			$respDbg['argData'] = $logData;
			$respDbg['columns'] = $dbColList;
			$respDbg['values'] = $dbValList;
			$response['debug'] = $respDbg;
			if (!$qResult) {
				// SQL ERROR
				$respData['sqlQuery'] = $query_string;
				$respData['result'] = 'Error logging data to OPEN log';
				$respData['sqlError'] =  mysqli_sqlstate($link);
				$respData['message'] = mysqli_error($link);
				$response['error'] = $respData;			
			} else {
				// success
				$respData['result'] = $qResult;
				$respData['message'] = 'Log record added to '.$logType.' log';
				$response['data'] = $respData;
			}
		} 
	} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		// query the database for the requested info
		// check the parameters
				
		$thisParam = 'sessionId';
		$sessionId = 0;
		if (array_key_exists($thisParam, $postData)) {
			$sessionId = trim($postData[$thisParam]);
			if (!is_numeric($sessionId)) {
				$badParam[$thisParam] =  "Not a number";
			}
		} else {
			$badParam[$thisParam] =  "Missing";
		}		

		$thisParam = 'taskId';
		$taskId = 0;
		if (array_key_exists($thisParam, $postData)) {
			$taskId = trim($postData[$thisParam]);
			if (!is_numeric($taskId)) {
				$badParam[$thisParam] =  "Not a number";
			}
		} else {
			// Task ID == 0 == All tasks in the session
			$taskId = 0;
		}				
//+
		if (empty($badParam)) {
			// no parameter errors, so get task configuration record
			// first get the open records
			$response['debug']['sessionId'] = $sessionId ;
			$response['debug']['taskId'] = $taskId ;
			
			if ($taskId > 0) {	
				$query = 'SELECT * FROM '.$DB_TABLE_TRANSITION_LOG.
					' WHERE taskId = '.$taskId. 
					' AND sessionId = '.$sessionId.
					' ORDER BY serverTimestamp ;';
			} else {
				// get all tasks for this session
				$query = 'SELECT * FROM '.$DB_TABLE_TRANSITION_LOG.
					' WHERE sessionId = '.$sessionId.
					' ORDER BY serverTimestamp ;';				
			}
			
			$response['debug']['query'] = $query;
			
			$result = mysqli_query ($link, $query);
			if ($result) {
				$openLogRecords = array();
				$openLogRecordCount = 0;
				while ($thisRecord = mysqli_fetch_assoc($result)) {
					unset($thisRecord['recordSeq']);
					if ($taskId > 0) {						
						$openLogRecords[$openLogRecordCount] = array_merge($thisRecord);
						$openLogRecordCount = $openLogRecordCount + 1;
					} else {
						$thisTask = $thisRecord['taskId'];
						if (empty($openLogRecords[$thisTask])) {
							$openLogRecords[$thisTask] = array();
						}
						array_push($openLogRecords[$thisTask], array_merge($thisRecord));
					}
				}
				$response['data'] = $openLogRecords;
			} else {
				// query error
				$respData['sqlQuery'] = $query;
				$respData['result'] = 'Error logging data to OPEN log';
				$respData['sqlError'] =  mysqli_sqlstate($link);
				$respData['message'] = mysqli_error($link);
				$response['error'] = $respData;		
			}
		} else {
			// bad or missing parameter
			$localErr = '';
			$localErr['message'] = 'Bad parameter in finish request.';
			$localErr['paramError'] = $badParam;
			$localErr['request'] = $postData;
			// $errData['globals'] = $GLOBALS;
			$errData['validation'] = $localErr;
		}
//-
	} else {
		// method not supported
		$errData['message'] = 'HTTP method not recognized. Method must be \'GET\' or \'POST\'';
		$response['error'] = $errData;
	}
	mysqli_close($link);
}	
if (!headers_sent()) {
	header('content-type: application/json');
	header('X-PHP-Response-Code: 200', true, 200);
}
if(empty($response)) { $response['error'] = "messed up"; }
print (json_encode($response));
?>

