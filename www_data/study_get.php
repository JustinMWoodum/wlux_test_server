<?php
require 'study_get_config.php';
require 'study_get_allids.php';

function _study_get($link, $postData) {
	// see if this is a config request
	$action = 'config';
	if (!empty($postData[$action])) {
		$logData = $postData[$action];
		$response = _study_get_config ($link, $logData);
	} else {
		$action = 'allIds';
		if (!empty($postData[$action])) {
			$logData = $postData[$action];
			$response = _study_get_allids ($link, $logData);
		} else {													
			// unrecognized command
			$errData['message'] = 'Action is not recognized. Action must be \'config\' or \'allIds\'.';
			$errData['postData'] = $postData;
			$errData['getData'] = $_GET;
			// $errData['globals'] = $GLOBALS;
			$response['error'] = $errData;
		}
	}
	return $response;
}
?>