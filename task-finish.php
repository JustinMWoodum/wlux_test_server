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
	
	$sessionId = 0;
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
        <title>WebLabUX Study Task Completion</title>
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
				$j.ajaxSetup({async: false}); 
				 // if undefined, use ""
				if (<?php echo $sessionId ?> > 0) {
					// start the task and get the configuration info
					var postResult = $j.post (taskURL, {"finish": {"sessionId" : <?php echo $sessionId; ?>, "taskId" : -1}},"json");
					
					postResult.done (function (response) {
							if (response.data !== undefined) {
								// alert("Going to: " + nextPage);
								$j("#pageHeading").text("Task "+ response.data.taskId + " completed");							
								$j("#taskInstructions").html("<p>Finish Time: " + response.data.finishTime + 
								"</p><h2>Task Instructions:</h2>" + response.data.finishPageHtml);
								// $j("#taskField").attr("value", response.data.taskId.toString());
								if (response.data.finishPageNextUrl.length > 0) {
									$j("#continueForm").attr("action", response.data.finishPageNextUrl);
									//$j("#continueForm").attr("method", "GET");
									$j("#continueBtn").attr("disabled",false);
								}
							} else {
								if (response.error !== undefined) {
									//display last task message
									$j("#pageHeading").text("Finished");	
									$j("#taskInstructions").html("<p>" + response.error.message + "<br/>Press <strong>Continue</strong> to finish.</p>");
									$j("#continueBtn").attr("disabled",false);
									$j("#continueForm").attr("action","study-end.php");
								}
							} 
							$j("#continueBtn").attr("disabled",false);
					});
				} else {
					$j("#textDiv").html("<p>No session was specified</p>");
				}
			}
		</script>
</head>

<body onload="getTask()">
<div id="pageContent" style="margin-left:auto; margin-right:auto; width:800px;">
<h1 id="pageHeading">Task x end</h1>
<div id="taskInstructions">
<p>These are the instructions for task x. Press <strong>Continue</strong> to begin.</p>
</div>
<div id="continueFormDiv">
<form id="continueForm" name="form1" method="POST" action="task-start.php">
    <input id="sessionField" type="hidden" name="wlux_session" value="<?php echo $sessionId; ?>" />
    <input id="continueBtn" type="submit" value="Continue" disabled />
</form>
</div>
</div>
</body>
</html>