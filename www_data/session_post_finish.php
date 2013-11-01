<?php
function _session_post_finish ($link, $logData) {
	require 'config_files.php';				
	// finish the session specified in the request
	$finishTime = time();
	$finishTimeText = date('Y-m-d H:i:s', $finishTime);
	// TODO: Need to check to see if this has been closed, already.
	//   if so, return an error, otherwise, update the record.

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
	
	if (empty($badParam)) {
		// close the task 0 record for this session
		$query = 'UPDATE '.$DB_TABLE_SESSION_LOG.
				 ' SET endTime = "'.$finishTimeText.
				 '" WHERE sessionId = '.$sessionId.
				 ' AND taskId = 0';
		$result = mysqli_query ($link, $query);
		// $response['debug']['query'] = $query;
		// $response['debug']['result'] = $result;						
		if ($result) {
			$rData = array();
			$rData['sessionId'] = $sessionId;
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