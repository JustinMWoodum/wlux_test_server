<?php
require 'gratuity_post_gratuity.php';
function _gratuity_post ($link, $postData) {	 
	// add a new record	
	$action = 'gratuity';
	if (!empty($postData[$action])) {
		$response = _gratuity_post_gratuity ($link, $postData[$action]);
	} else {
		$localErr = '';
		$localErr['request'] =  $postData;
		$localErr['message'] = 'Unrecognized command in request buffer.';
		$errData['postRequest'] = $localErr;
		$response['error'] = $errData;
	}								
	return $response;	
}
?>