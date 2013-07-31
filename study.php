<?php 
require 'config_files.php';

$DB_SERVER = 'localhost';
$response = '';

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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
		
		// see if this is a config request
		$action = 'config';
		$logData = $postData[$action];
		if (!empty($logData)) {
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
			$errData['message'] = 'Action is not recognized. Action must be \'config\'.';
			$errData['postData'] = $postData;
			$errData['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$response['error'] = $errData;
		}
	} else {
		// method not supported
		$errData['message'] = 'HTTP method not recognized. Method must be \'GET\'';
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