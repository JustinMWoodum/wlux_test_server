<?php
include 'config_files.php';
// Logs page transitions on a weblabux study site.

// In the final implementation, this will obviously write data to
// a database instead of a text file.

//$type = $_POST["type"];  // the type of action we're logging
//$data_arr = array();
$json = $_POST["data"];
$session = $json["wlux_session"];
$conditionId = $json["conditionId"];

if (!empty($conditionId) && !empty($session) && !empty($json)) {
    $data = "log_entry_time:\t".date('c')."\n";
    while (list($key, $value) = each($json)) {
        $data = $data . "\t". $key . ":\t" . $value . "\n";
    }
    $data = $data . "\n";

    $file = $sessionLogFolder . "session" . $session . ".txt";
    $fileResult = file_put_contents($file, $data, FILE_APPEND);
	
	if ($fileResult) {
		$response['data'] = $fileResult."bytes written to: ".$file;
	} else {
	 	$errData['message'] = 'Unable to log data to log file: '.$file;
		$response['error'] = $errData;
	}
} else {
	// send error response
	$errData['message'] = 'Data is not formatted correctly ';
	$errData['params'] = $json;
	$response['error'] = $errData;
}
if (!headers_sent()) {
	header('content-type: application/json');
	header('X-PHP-Response-Code: 200', true, 200);
}
print (json_encode($response));

// otherwise we got an invalid request - just don't write anything to the file

?>

