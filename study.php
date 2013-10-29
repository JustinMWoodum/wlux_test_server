<?php 
require 'config_files.php';
require 'study_get.php';
require 'study_post.php';

$DB_SERVER = 'localhost';
$response = '';

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$response = _study_get($link);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$response = _study_post($link);
	} else {
		// method not supported
		$errData['message'] = 'HTTP method not recognized. Method must be \'GET\'';
		$response['error'] = $errData;
	}
	mysqli_close($link);
}

if (!headers_sent()) {
	header('content-type: application/json');
	header('X-PHP-Response-Code: 200', true, 200);
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