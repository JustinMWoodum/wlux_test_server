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
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {		
		// see if this is a config request
		$action = 'studyId';
		if (!empty($postData[$action])) {
			$logData = $postData[$action];
			// return the specified configuration
			
			// check the parameters
			$thisParam = 'studyId';
			if (!is_numeric($logData)) {
				$badParam[$thisParam] =  "Not a number";
			} else {
				$studyId = $logData;
			}
	
			if (empty($badParam)) {										
				// read conifguration for this study and condition
				$query = 'SELECT * FROM '.$DB_TABLE_GRATUITY_LOG.' WHERE studyId = '.$studyId;
				$result = mysqli_query ($link, $query);
				if (mysqli_num_rows($result)  > 0) {
					$idx = 0;
					while ($thisRecord = mysqli_fetch_assoc($result))  {
						// remove the recordSeq field
						unset($thisRecord['recordSeq']);
						$response['data'][$idx] = array_merge($thisRecord);
						foreach ($response['data'][$idx] as $k => $v) {
							// set "null" strings to null values
							if ($v == 'NULL') {
								$response['data'][$k] = NULL;
							}
						}
						$idx += 1;
					}
				}
				if ($idx == 0) {
					$localErr = '';
					$localErr['sqlQuery'] = $query;
					$localErr['result'] = 'No gratuity records found';
					$localErr['dataRecord'] = $thisRecord;
					$localErr['sqlError'] =  mysqli_sqlstate($link);
					$localErr['message'] = mysqli_error($link);
					$errData['queryData'] = $localErr;
					$response['error'] = $errData;		
				}
			} else {
				// bad parameter in request data
				$errData['message'] = 'studyId is missing from the query string.';
				$errData['paramError'] = $badParam;
				$errData['request'] = $logData;
				// $errData['globals'] = $GLOBALS;
				$response['error'] = $errData;		
			}
		} else {						
			// unrecognized command
			$errData['message'] = 'Action is not recognized. Action must be \'study\'.';
			$errData['postData'] = $postData;
			$errData['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$response['error'] = $errData;
		}
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// add a new record	
		$action = 'gratuity';
		if (!empty($postData[$action])) {
			$logData = $postData[$action];
			// create a new gratuity_log record 
			if (!empty($logData)) {
				// TODO: Chceck fields

				// add server-generated fields to insert query
				$dbColList = 'recordSeq';
				$dbValList = '0';
												
				// add the client-provided fields	
				foreach ($logData as $dbCol => $dbVal) {
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
				$queryString = 'INSERT INTO '.$DB_TABLE_GRATUITY_LOG.' ('.$dbColList.') VALUES ('.$dbValList.')';
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
					$response['data'] = $logData;
				} 					
			}
		} else {
			$localErr = '';
			$localErr['request'] =  $postData;
			$localErr['message'] = 'Unrecognized command in request buffer.';
			$errData['postRequest'] = $localErr;
		}								
	} else {
		// method not supported
		$errData['message'] = 'HTTP method not recognized. Method must be \'GET\' or \'POST\'.';
		$response['error'] = $errData;
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