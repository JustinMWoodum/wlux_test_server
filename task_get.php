<?php
require 'task_get_config.php';

function _task_get($link, $postData) {
	$action = 'config';
	if (!empty($postData[$action])) {
		$response = _task_get_config ($link, $postData[$action]);
		
	} else {
			// unrecgnized command
			$localErr = '';
			$localErr['message'] = 'Unrecognized command. Command must be "config".';
			$localErr['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$errData['command'] = $localErr;
	}
	
	if (!empty($errData)) {
		$response['error'] = $errData;
	}

	return $response;
}
?>