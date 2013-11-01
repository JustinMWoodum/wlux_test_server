<?php
require 'session_post_start.php';
require 'session_post_finish.php';

function _session_post ($link, $postData) {
	// determine log type from variable name
	//	 $respDbg['globals'] = $GLOBALS;
	$respDbg['argData'] = $postData;
	$response['debug'] = $respDbg;	

	$action = 'start';
	if (!empty($postData[$action])) {
		$logData = $postData[$action];
	}

	if (!empty($logData)) {
		$response = _session_post_start($link, $logData);
	} else {
		// see if this is a finish request
		$action = 'finish';
		//$response['debug'] = $postData;
		if (!empty($postData[$action])) {
			$response = _session_post_finish($link, $postData[$action]);
		} else {
			// unrecognized command
			$localErr = '';
			$localErr['message'] = 'Action is not recognized. Action must be \'config\', \'start\', or \'finish\'';
			$localErr['postData'] = $postData;
			$localErr['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$errData['command'] = $localErr;
			$response['error'] = $errData;
		}
	}

	return $response;
		
}

?>