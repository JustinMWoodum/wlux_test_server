<?php

function _study_post($link) {
	// method not supported
	$errData['message'] = 'HTTP method not recognized. Method must be \'GET\'';
	$response['error'] = $errData;
	
	return $response;
}


?>