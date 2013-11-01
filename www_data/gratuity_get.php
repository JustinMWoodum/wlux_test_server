<?php 
require 'gratuity_get_studyid.php';

function _gratuity_get($link, $postData) {
	// see if this is a config request
	$action = 'studyId';
	if (!empty($postData[$action])) {
		$response = _gratuity_get_studyId ($link, $postData[$action]);
	} else {						
		// unrecognized command
		$errData['message'] = 'Action is not recognized. Action must be \'study\'.';
		$errData['postData'] = $postData;
		$errData['getData'] = $_GET;
		// $errData['globals'] = $GLOBALS;
		$response['error'] = $errData;
	}
	return $response;
}
?>