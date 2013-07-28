<?php
	// Contains paths to files and folders shared by the 
	//  php scripts on the server.

	// "data base" of session information
	//   for development, we're just using a text file
	$sessionDataFile = "data_files/sessions.txt";
	
	// folder where session transaction data is logged
	$sessionLogFolder = "study_results/";

	// folder where the session config file is stored
	$CONFIG_FILE_PATH = "./config_file/";
	$CONFIG_FILE_NAME = "config.txt";
	
	// database interfaces
	$DB_USER = 'db_test';
	$DB_PASS = 'WeCantDecide2';
	$DB_DATABASE_NAME = 'db_test';
	$DB_TABLE_OPEN_LOG = 'log_open';
	$DB_TABLE_TRANSITION_LOG = 'log_transition';
	$DB_TABLE_SESSION_CONFIG = 'session_config';
	$DB_TABLE_PARTICIPANT_LOG = 'participant_log';
?>
