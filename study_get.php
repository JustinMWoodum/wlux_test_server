<?php
require 'study_get_config.php';

function _study_get($link) {
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
	
	// see if this is a config request
	$action = 'config';
	if (!empty($postData[$action])) {
		$logData = $postData[$action];
		$response = _study_get_config ($link, $logData);
	} else {						
		// unrecognized command
		$errData['message'] = 'Action is not recognized. Action must be \'config\'.';
		$errData['postData'] = $postData;
		$errData['getData'] = $_GET;
		// $errData['globals'] = $GLOBALS;
		$response['error'] = $errData;
	}
	return $response;
}
?>