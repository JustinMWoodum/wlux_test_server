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
			
			$queryString = 'INSERT INTO '.$logTable.' ('.$dbColList.') VALUES ('.$dbValList.')';
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
		if (empty($postData[$thisParam]) || !is_numeric($postData[$thisParam])) {
			if (empty($postData[$thisParam])) {
				$badParam[$thisParam] =  "Missing";
			} else {
				$badParam[$thisParam] =  "Not a number";
			}
		}
		
		$thisParam = 'taskId';
		if (empty($postData[$thisParam]) || !is_numeric($postData[$thisParam])) {
			if (empty($postData[$thisParam])) {
				$badParam[$thisParam] =  "Missing";
			} else {
				$badParam[$thisParam] =  "Not a number";
			}
		}
		
		if (empty($badParam)) {
			// no parameter errors, so get task configuration record
			// first get the open records
			$query = 'SELECT * FROM '.$DB_TABLE_OPEN_LOG.
				' WHERE sessionId = '.$postData['sessionId'].
				' AND taskId = '.$postData['taskId'].
				' ORDER BY serverTimestamp';
			$result = mysqli_query ($link, $query);
			$openLogRecords = array();
			$openLogRecordCount = 0;
			if (mysqli_num_rows($result) > 0 ) {
				while ($thisRecord = mysqli_fetch_assoc($result)) {
					$openLogRecordCount = $openLogRecordCount + 1;
					$openLogRecords[$openLogRecordCount] = $thisRecord;
				}
			}
			
			$query = 'SELECT * FROM '.$DB_TABLE_TRANSITION_LOG.
				' WHERE sessionId = '.$postData['sessionId'].
				' AND taskId = '.$postData['taskId'].
				' ORDER BY serverTimestamp';
			$result = mysqli_query ($link, $query);
			$transitionLogRecords = array();
			$transitionLogRecordCount = 0;
			if (mysqli_num_rows($result) > 0 ) {
				while ($thisRecord = mysqli_fetch_assoc($result)) {
					$transitionLogRecordCount = $openLogRecordCount + 1;
					$transitionLogRecords[$transitionLogRecordCount] = $thisRecord;
				}
			}

			if (!empty($openLogRecords)) {
				$response['data']['open']['count'] = $openLogRecordCount;
				$response['data']['open']['data'] = $openLogRecords;
			} else {
				$response['data']['open']['count'] = 0;
				$response['data']['open']['data'] = null;
			}
			
			if (!empty($transitionLogRecords)) {
				$response['data']['transition']['count'] = $transitionLogRecordCount;
				$response['data']['transition']['data'] = $transitionLogRecords;
			} else {
				$response['data']['transition']['count'] = 0;
				$response['data']['transition']['data'] = null;
			}
		} else {
			// bad or missing parameter
			$localErr = '';
			$localErr['message'] = 'Bad parameter in finish request.';
			$localErr['paramError'] = $badParam;
			$localErr['request'] = $logData;
			// $errData['globals'] = $GLOBALS;
			$errData['validation'] = $localErr;
		}
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

print (json_encode($response));
?>

