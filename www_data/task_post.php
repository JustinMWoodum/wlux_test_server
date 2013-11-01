<?php
require 'task_post_start.php';
require 'task_post_finish.php';

function _task_post ($link, $postData) {
    // $respDbg['globals'] = $GLOBALS;
	$action = 'start';
	if (!empty($postData[$action])) {
		$response = _task_post_start ($link, $postData[$action]);
	} else {
		// see if this is a finish request
		$action = 'finish';								
		if (!empty($postData[$action])) {
			//$response['debug'] = $postData;
			$response = _task_post_finish ($link, $postData[$action]);
		} else {
			// unrecognized command
			$localErr = '';
			$localErr['message'] = 'Action is not recognized. Action must be \'config\', \'start\', or \'finish\'';
			$localErr['postData'] = $postData;
			$localErr['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$errData['command'] = $localErr;
		}
	}

	if (!empty($errData)) {
		$response['error'] = $errData;
	}
	return $response;	
}
?>