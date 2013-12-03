<?php

	/* Initial code added by justin.woodum@gmail.com

	Much of this code was borrowed from task.php

	This has remaining parts that need to be completed. 
	Certain parts have been marked nearby with the tags shown below to break this work into tasks.

		ToDoDevQuestion			Question for senior developer, or to be investigated further.
		ToDoLoadValues			Variables that still need to be assigned values correctly.	
		ToDoErrorMessages		Question for whoever revises error messages.
		ToDoParameters			Set acceptible parameter values.
		ToDoDelay				Things that are being put off until later.
		ToDoReady				Thing that can be done any time.

	*/
	
	echo AddStudyConfig();
	
	function AddStudyConfig() {
	
	/* -- Load libraries -------------------------------------------------------------------------------------------------- */
	
		require 'config_files.php';
	
	/* -- Get data -------------------------------------------------------------------------------------------------------- */

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

	/* -- Open database -------------------------------------------------------------------------------------------------------- */	
		
		// open database
		$link = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_DATABASE_NAME);

		// see if connecting to database succeeded
		if (!$link) {

	/* -- Opening database failed ------------------------------------------------------------------------------------------------ */

			// can't open DB so return an error
			// this line only works on PHP > 5.4.0, which not everyone seems to have.
			//   http_response_code(500);
			// this works on PHP > 4.3 (or so)
			$localErr = '';
			$localErr['message'] = 'Can\'t connect to server: '.$DB_SERVER.' as: '.$DB_USER;
			$errData['dbconnect'] = $localErr;

		} else {

	/* -- Opening database succeeded ---------------------------------------------------------------------------------------------- */

	/* -- Get & validate arguments ---------------------------------------------------------------------------------------------- */
		
			// Get inputted values
			$study_config['studyId'] = $postData['studyId'];
			$study_config['taskId'] = $postData['taskId'];
			$study_config['conditionId'] = $postData['conditionId'];

			// Validate inputted values
			// filter_var usage borrowed from: http://php.net/manual/en/function.filter-var.php

			/* Validate $study_config['studyId'] */
			
			// ToDoParameters: what should minimum & max ranges be?
			$options = array(
				'options' => array('min_range' => 0, 'max_range' => 9999)
			);

			if (filter_var($study_config['studyId'], FILTER_VALIDATE_INT, $options) == FALSE) {
				// bad parameter
				$localErr = '';
				$localErr['message'] = 'Bad parameter.';
				$localErr['paramError']['studyId'] = "Must be a number between 0 and 9999.";
				$localErr['request'] = $logData;
				$errData['validation'] = $localErr;
			}

			/* Validate $study_config['taskId'] */

			// ToDoParameters: what should minimum & max ranges be? */
			$options = array(
				'options' => array('min_range' => 0, 'max_range' => 9999)
			);

			if (filter_var($study_config['taskId'], FILTER_VALIDATE_INT, $options) == FALSE) {
				// bad parameter
				$localErr = '';
				$localErr['message'] = 'Bad parameter.';
				$localErr['paramError']['taskId'] = "Must be a number between 0 and 9999.";
				$localErr['request'] = $logData;
				$errData['validation'] = $localErr;
			}			

			/* Validate $study_config['conditionId'] */

			// ToDoParameters: what should minimum & max ranges be? */
			$options = array(
				'options' => array('min_range' => 0, 'max_range' => 9999)
			);

			if (filter_var($study_config['conditionId'], FILTER_VALIDATE_INT, $options) == FALSE) {
				// bad parameter
				$localErr = '';
				$localErr['message'] = 'Bad parameter.';
				$localErr['paramError']['conditionId'] = "Must be a number between 0 and 9999.";
				$localErr['request'] = $logData;
				$errData['validation'] = $localErr;
			}			

	/* -- Determine what sort of submission method was used for data ----------------------------------------------------------------------------------- */
			
			/* Attempt to add new configuration to database, or return error */

			// check to see what type of submission method was used
			$requestMethod = $_SERVER['REQUEST_METHOD'];

			switch ($requestMethod) {

	/* -- If submission method was POST ----------------------------------------------------------------------------------------------------- */
			
				case 'POST':

	/* -- Check to see if configuration exists ------------------------------------------------------------------------------------------ */

					$query = 'SELECT * FROM '. $DB_TABLE_STUDY_CONFIG .
						' WHERE studyId = '. $study_config['studyId'] .
						' AND taskId = ' . $study_config['taskId'] .
						' AND conditionId = ' . $study_config['conditionId'];

					$result = mysqli_query ($link, $query);
					
	/* -- If existing configuration found, return error -------------------------------------------------------------------------------------- */
					
					if (mysqli_num_rows($result) != 0) {

						$localErr = '';
						$localErr['message'] = 'Study configuration already exists.';
						
						// ToDoErrorMessages: How to convey combination of these variables already exists?
						$localErr['paramError']['studyId'] = "";  
						$localErr['paramError']['taskId'] = "";
						$localErr['paramError']['conditionId'] = "";

						// ToDoDevQuestion: is this correct way to use $logData?
						$localErr['request'] = $logData;
						
						// ToDoDevQuestion: correct $errData property to load error into?
						$errData['queryConfig'] = $localErr;

					} else {

	/* -- If configuration not found, add it --------------------------------------------------------------------------------------- */				

						/* ToDoReady: This should use response['config'] instead of explicitely calling each field variable
						$query = 'INSERT INTO ' . $DB_TABLE_STUDY_CONFIG .
							'VALUES ' = . $recordSeq .
							' ' = . $study_config['studyId'] .
							' ' = . $sessionId .
							' ' = . $study_config['taskId'] .
							' ' = . $study_config['conditionId']; */
							/* ToDoLoadValues: remaining fields need to be added to new record
							' ' = . $conditionCssUrl .
							' ' = . $taskBarCssUrl .
							' ' = . $startUrl .
							' ' = . $returnUrl .
							' ' = . $buttonText .
							' ' = . $tabShowText .
							' ' = . $tabHideText .
							' ' = . $taskText .
							' ' = . $taskHtml .
							' ' = . $startPageHtml .
							' ' = . $finishPageHtml .
							' ' = . $startPageNextUrl .
							' ' = . $finishPageNextUrl .
							' ' = . $measuredTask .
							' ' = . $taskType;
							*/
						/*
						$result = mysqli_query ($link, $query);

						if (!($result)) {
						
							// error: database query failed
							$localErr = '';
							$localErr['sqlQuery'] = $query;
							$localErr['result'] = 'Error adding configuration.';
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['queryConfig'] = $localErr;

						} */
						/*
						} else {
							run mysql command INSERT;
							return new complete record with "recordSeq" field stripped out;
						}
						*/
					
						// create a new session_cofig record for this session
						echo "Add recording... ";
						
						$studyTaskConfig = $studyConfigRecords[$currentTask];
						$studyTaskConfig['recordSeq'] = NULL;
						#$studyTaskConfig['sessionId'] = $newTaskRecord['sessionId'];

						// add server-generated fields to insert query
						$dbColList = 'autoConditionId';
						$dbValList = '0';
														
						// add the client-provided fields	
						foreach ($studyTaskConfig as $dbCol => $dbVal) {
							isset($dbColList) ? $dbColList .= ', ' : $dbColList = '';
							isset($dbValList) ? $dbValList .= ', ' : $dbValList = '';										
							$dbColList .= $dbCol;
							if (empty($dbVal) && (strlen($dbVal)==0)) {
								$dbValList .= 'NULL';
							} else {
								$escapedString = str_replace("'","''",$dbVal);
								$dbValList .= '\''.$escapedString.'\'';
							}							
						}
						$queryString = 'INSERT INTO '.$DB_TABLE_STUDY_CONFIG.' ('.$dbColList.') VALUES ('.$dbValList.')';
						$qResult = mysqli_query($link, $queryString);
						if (!$qResult) {
							// SQL ERROR
							$localErr = '';
							$localErr['sqlQuery'] = $queryString;
							$localErr['result'] = 'Error creating session_config record';
							$localErr['sqlError'] =  mysqli_sqlstate($link);
							$localErr['message'] = mysqli_error($link);
							$errData['insert1'] = $localErr;
						} else {
							// finish start response buffer
							$response['data']['startPageHtml'] = $studyTaskConfig['startPageHtml'];
							$response['data']['startPageNextUrl'] = $studyTaskConfig['startPageNextUrl'];
							$response['data']['finishPageNextUrl'] = $studyTaskConfig['finishPageNextUrl'];
							$response['data']['taskType'] = $studyTaskConfig['taskType'];

							// remove the recordSeq field
							unset($thisRecord['recordSeq']);
							$response['data'] = array_merge($thisRecord);
							foreach ($response['data'] as $k => $v) {
								// set "null" strings to null values
								if ($v == 'NULL') {
									$response['data'][$k] = NULL;
							}
						}
					}
					
					break;
				// ToDoDelay
				//case 'PUT':
				//	/*
				//	run mysql command INSERT using inputted fields;
				//	*/
				#	break;
				}

			}

	/* -- Finished; close database -------------------------------------------------------------------------------------------------- */

			mysqli_close($link);
		
		}

	/* -- If there was an error above, place it in response buffer ------------------------------------------------------------------ */	
		
		if (!empty($errData)) {
			$response['error'] = $errData;
		}
		
	/* -- No matter what, return the response buffer --------------------------------------------------------------------------------- */
		
		$fnResponse = json_encode($response);
		
		return $fnResponse;
		
	}
?>