<?php

function _study_post($link, $postData) {
	// TODO: Look for commands in $postData and pass the 
	//  command object to the corresponding function for processing.
	//  Function called from here should return a complete response buffer.
	//
	//  in the mean time, return an error message.	
	//
	// method not supported
	$errData['message'] = 'HTTP method not recognized. Method must be \'GET\'';
	$response['error'] = $errData;
	
	return $response;
}


?>