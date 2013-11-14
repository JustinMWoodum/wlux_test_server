<?php
require 'log_get_log.php';
require 'log_get_allids.php';

function _log_get ($link, $postData) {
	
	$thisParam = 'allIds';
	if (array_key_exists($thisParam, $postData)) {
		$response = log_get_allids ($link, $postData[$thisParam]);
	} else {
		$response = log_get_log($link, $postData);
	}
	return $response;
}
?>
