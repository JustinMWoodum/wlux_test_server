<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// get session ID from query string
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
	
	$studyId = 0;
	if (!empty($postData['wlux_session'])) {
		$sessionId = $postData['wlux_session'];
	}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Task Start</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
        h2 { margin-top: 1em; }
        div.container { width: 500px; margin: auto; }
        </style>
		<script src="jquery.js" type="text/javascript"></script>
		<script type="text/javascript">
			
			$j = jQuery.noConflict();
			
			var host = window.location.host;
			var LOCAL = (host.indexOf("localhost") != -1) ||
						(host.indexOf("127.0.0.1") != -1);
		
			var taskURL = "http://wlux.uw.edu/rbwatson/task.php";
			
			if (LOCAL) {
				taskURL = "/rbwatson/task.php";
			}
			
			function getTask () {
				// get session info
				$j.ajaxSetup({async: false}); //
				 // if undefined, use ""
				if (<?php echo $sessionId ?> > 0) {
					// start the task and get the configuration info
					var postResult = $j.post (taskURL, {"start": {"sessionId" : <?php echo $sessionId ?>}},"json");
					
					postResult.done (function (response) {
							var nextPage="task1.php?wlux_session="+response.data.sessionId.toString();
							// alert("Going to: " + nextPage);
							$j("#pageHeading").text("Task "+ response.data.taskId);
							$j("#taskInstructions").html("<p>SessionId: "+ response.data.sessionId + "<br/>StartTime: " + response.data.startTime + "</p>");
//							$j("#sessionField").attr("value", response.data.sessionId.toString());
//							$j("#continueBtn").attr("disabled",false);
						});			
				} else {
					$j("#textDiv").html("<p>No session was specified</p>");
				}
			}
		</script>
</head>

<body onload="getTask()">
<h1 id="pageHeading">Task x</h1>
<div id="taskInstructions">
<p>These are the instructions for task 1. Press <strong>Continue</strong> to begin.</p>
</div>
<button onclick="window.location('task-start.php')">Continue</button>

</body>
</html>