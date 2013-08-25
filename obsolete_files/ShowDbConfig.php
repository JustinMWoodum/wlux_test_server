<?php 
require 'config_files.php';

$DB_SERVER = 'localhost';

$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);
if (!$link) {
	// can't open DB so return an error
	// this line only works on PHP > 5.4.0, which not everyone seems to have.
	//   http_response_code(500);
	// this works on PHP > 4.3 (or so)
	if (!headers_sent()) {
		header('content-type: application/json');
		header('X-PHP-Response-Code: 200', true, 200);
	}
	$errData['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
	$response['error'] = $errData;
} else {
	$sessionId = $_GET['sessionId'];	
	$query_string = "SELECT * FROM ".$DB_TABLE_SESSION_CONFIG." WHERE studyId = ".$sessionId;
//	print ('QueryString: '.$query_string.'<br>');
	$result = mysqli_query ($link, $query_string);
	if (mysqli_num_rows($result) > 0) {
//		print (mysqli_num_rows($result)." rows returned<br>");
		if ($thisRecord = mysqli_fetch_assoc($result))  {
			if (!headers_sent()) {
				header('content-type: application/json');
				header('X-PHP-Response-Code: 200', true, 200);
			}
			$response['data'] = $thisRecord;
		}
	} else {
		if (!headers_sent()) {
			header('content-type: application/json');
			header('X-PHP-Response-Code: 200', true, 200);
		}
		$errData['message'] = mysqli_error($link);
		$errData['sqlQuery'] = $query_string;
		$errData['sqlError'] = mysqli_sqlstate($link);
		$response['error'] = $errData;
	}	
	
	print (json_encode($response));
	mysqli_close($link);
}
?>