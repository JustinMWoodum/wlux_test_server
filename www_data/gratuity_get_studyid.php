<?php
function _gratuity_get_studyId ($link, $logData) {	
require 'config_files.php';
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
		$idx = 0;
		if (mysqli_num_rows($result)  > 0) {
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
	return $response;
}
?>