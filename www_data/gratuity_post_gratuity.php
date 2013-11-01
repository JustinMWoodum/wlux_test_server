<?php
function _gratuity_post_gratuity ($link, $logData) {
require 'config_files.php';
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
			$response['error'] = $errData;
		} else {
			// finish start response buffer
			$response['data'] = $logData;
		} 					
	}
	return $response;
}
?>