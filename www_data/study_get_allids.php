<?php
function _study_get_allids ($link, $logData) {
	require 'config_files.php';
	// return the specified configuration
	$query = 'SELECT DISTINCT studyId FROM '.$DB_TABLE_STUDY_CONFIG;
	$result = mysqli_query ($link, $query);
	$recordIndex = 0;
	$response['data']['count'] = mysqli_num_rows($result);
	if ($response['data']['count'] > 0) {
		while ($thisRecord = mysqli_fetch_assoc($result)) {
			$response['data']['studyIds'][$recordIndex] = $thisRecord['studyId'];
			$recordIndex = $recordIndex + 1;
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
	
	return $response;
}
?>