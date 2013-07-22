<?php
	include 'config_files.php';
    // set the returnURL dynamically depending on whether we're on the
    // development or produciton environment.
    // NOTE: this assumes that the site and server directories are in the root
    // of the site only when developing on localhost

    $data = array();
    $session = $_GET["wlux_session"];
	$test = $_GET["test"];
	$testMode = false;
	
	if ($test == "true") {
		$testMode = true;
	}

	$LOCAL = is_dir("../wlux_test_site");
	$serverRoot = "http://wlux.uw.edu/rbwatson/";
	if ($LOCAL) {
		$serverRoot = "/server/";
	}
	     
    if (file_exists($CONFIG_FILE_PATH.$CONFIG_FILE_NAME)) {
		// read config from file
        $data = unserialize(file_get_contents($CONFIG_FILE_PATH.$CONFIG_FILE_NAME));
		if ($testMode) {
			// format and send output
			header('content-type: text/plain');
			header("HTTP/1.1 200 Success");
			echo "File name: ".$CONFIG_FILE_PATH.$CONFIG_FILE_NAME."\n";
			echo file_get_contents($CONFIG_FILE_PATH.$CONFIG_FILE_NAME);
			echo "JSON:";
			echo json_encode($data);
		} else {
			if ($data["conditionId"] == "" || $data["cssURL"] == "") {
				// file parsing error
				// this will trigger the jquery ajax call's error handling callback
				header("HTTP/1.1 404 Not Found");
			} else {
				// wrap data in jsonp format
				$jsonpTag = $_GET["callback"]; // set by jquery ajax call when using jsonp data type
				if (!empty($jsonpTag)) { 
					// format and send output
					header('content-type: application/json');
					echo $jsonpTag . '(' . json_encode($data) . ')';
				} else {
					// no callback param name so return an error
					// this line only works on PHP > 5.4.0, which not everyone seems to have.
					//   http_response_code(500);
					// this works on PHP > 4.3 (or so)
					if (!headers_sent()) {
						header('X-PHP-Response-Code: 500', true, 500);
					}
				} 
			}
		}
	} else {
		// this line only works on PHP > 5.4.0, which not everyone seems to have.
		//   http_response_code(500);
		// this works on PHP > 4.3 (or so)
		if (!headers_sent()) {
			header('X-PHP-Response-Code: 500', true, 500);
		}
		// else too late to send a header with an error code
	}
?>
