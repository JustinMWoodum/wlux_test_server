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
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
        h2 { margin-top: 1em; }
        div.container { width: 500px; margin: auto; }
        </style>
        <title>WebLabUX Study Thanks!</title>
		<script src="jquery.js" type="text/javascript"></script>
		<script type="text/javascript">
			
			$j = jQuery.noConflict();
			
			var host = window.location.host;
			var LOCAL = (host.indexOf("localhost") != -1) ||
						(host.indexOf("127.0.0.1") != -1);
		
			var sessionURL = "http://wlux.uw.edu/rbwatson/session.php";
			
			if (LOCAL) {
				sessionURL = "/rbwatson/session.php";
			}
			
			function finishSession () {
				// get session info
				$j.ajaxSetup({async: false});
				 // if undefined, use ""
				if (<?php echo $sessionId ?> > 0) {
					var finishResult = $j.post (sessionURL, {"finish": {"sessionId" : <?php echo $sessionId ?>}},"json");
					var configResult = $j.get (sessionURL, {"config": {"sessionId" : <?php echo $sessionId ?>, "taskId": -1}},"json")
					configResult.done (function (response) {
							$j("#studyField").attr("value",response.data.studyId);
							// logged the finish so enable the button to exit
							$j("#showMeDiv").html("<p><a href=\"http://wlux.uw.edu/rbwatson/log.php?sessionId=" + response.data.sessionId + "\" target=\"_blank\">View the log file</a></p>");
							$j("#continueBtn").attr("disabled",false);
							$j("#prompt").css("display","block");
						});			
				} else {
					$j("#textDiv").html("<p>No study session was specified</p>");
				}
			}
		</script>
    </head>

    <body onLoad="finishSession()">
        <div class="container">
            <h2>WebLabUX Study Thanks!</h2>
            <div id="textDiv">
                <p>Thank you for participating in our study.</p>
                <p id="prompt" style="display:none">Click <strong>Continue</strong> to exit.</p>
            </div>
            <div id="showMeDiv">
            </div>
            <!-- go back to start -->
            <form id="continueButton" name="form1" method="POST" action="start.php">
                <input id="studyField" type="hidden" name="wlux_study" value="0" />
                <input id="continueBtn" type="submit" value="Continue" disabled />
            </form>
        </div> <!-- container -->
    </body>
</html>
