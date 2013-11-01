<?php
require 'session_get_config.php';
require 'session_get_log.php';

function _session_get($link, $postData) {
	// see if this is a finish request
	$action = 'config';
	if (!empty($postData[$action])) {
		// return the specified configuration
		$response = _session_get_config ($link, $postData[$action]);
	} else {
		$action = 'log';
		if (!empty($postData[$action])) {
			// return the task info from the log
			$response = _session_get_log ($link, $postData[$action]);
		} else {			
			// unrecognized command
			$localErr = '';
			$localErr['message'] = 'Action is not recognized. Action must be \'config\', or \'log\'';
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