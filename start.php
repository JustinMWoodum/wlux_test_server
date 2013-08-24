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
	if (!empty($postData['studyId'])) {
		$studyId = $postData['studyId'];
	}

?>
<html>
    <head>
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
		
			var sessionURL = "http://wlux.uw.edu/rbwatson/session.php";
			
			if (LOCAL) {
				sessionURL = "/rbwatson/session.php";
			}
			
			function getSession () {
				// get session info
				$j.ajaxSetup({async: false}); //
				 // if undefined, use ""
				if (<?php echo $studyId ?> > 0) {
					var postResult = $j.post (sessionURL, {"start": {"studyId" : <?php echo $studyId ?>}},"json");
					
					postResult.done (function (response) {
							// alert("Going to: " + nextPage);
							$j("#sessionField").attr("value", response.data.sessionId.toString());
							$j("#continueBtn").attr("disabled",false);
						});			
				} else {
					$j("#textDiv").html("<p>No study was specified</p>");
				}
			}
		</script>
    </head>

    <body onLoad="getSession()">
        <div class="container">
            <h2>WebLabUX Survey Consent Form</h2>
            <div id="textDiv">
                <p>Thank you for agreeing to participate in our survey.<br />
                Please click on continue to begin the survey</p>
            </div>
            <!-- <button onClick="navToFirstTask();" value="ClickMe">Click Me</button> -->
            <form id="continueButton" name="form1" method="POST" action="task-start.php">
                <input id="sessionField" type="hidden" name="wlux_session" value="0" />
                <input id="continueBtn" type="submit" value="continue" disabled />
            </form>
        </div> <!-- container -->
    </body>
</html>
