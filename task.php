<?php 
require 'config_files.php';
require 'task_get.php';
require 'task_post.php';

$DB_SERVER = 'localhost';
$response = '';
$badParam = null;

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

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$localErr = '';
	$localErr['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$errData['dbconnect'] = $localErr;
} else {
	// connected to database, check for a get request
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$response = _task_get($link, $postData);
	} else {
		// check for a POST request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
 			$response = _task_post ($link, $postData);
 	    } else {
			// method not supported
			$localErr = '';
			$localErr['message'] = 'HTTP method not recognized. Method must be \'GET\' or \'POST\'';
			$errData['method'] = $localErr;
		}
	}
	mysqli_close($link);
}

if (!headers_sent()) {
	header('content-type: application/json');
	header('X-PHP-Response-Code: 200', true, 200);
}
if (!empty($errData)) {
	$response['error'] = $errData;
}

$thisParam = "callback";
if (array_key_exists($thisParam, $_GET)) {
	$jsonpTag = $_GET[$thisParam]; // set by jquery ajax call when using jsonp data type
}

if (!empty($jsonpTag)) { 
	// format and send output
	// no error information is returned in the JSONP response!
	$fnResponse = $jsonpTag . '(' . json_encode($response['data']) . ')';
} else {
	// no callback param name so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$fnResponse = json_encode($response);
} 
print ($fnResponse);
?>