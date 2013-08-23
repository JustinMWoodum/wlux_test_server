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
	
		var sessionURL = "http://wlux.uw.edu/rbwatson/logger.php";
		
		if (LOCAL) {
			sessionURL = "/rbwatson/session.php";
		}
		
		function getSession () {
			// get session info
			$j.ajaxSetup({async: false}); //
			 // if undefined, use ""
			var postResult = $j.post (sessionURL, {"start": {"studyId" : 1234}},"json");
			
			postResult.done (function (response) {
					var nextPage="task1.php?wlux_session="+response.data.sessionId.toString();
					// alert("Going to: " + nextPage);
					$j("#sessionField").attr("value", response.data.sessionId.toString());
					$j("#continueBtn").attr("disabled",false);
				});			
		}
		</script>
    </head>

    <body onload="getSession()">
        <div class="container">
            <h2>WebLabUX Survey Consent Form</h2>

            <p>Thank you for agreeing to participate in our survey.<br />
            Please click on continue to begin the survey</p>
            <!-- <button onClick="navToFirstTask();" value="ClickMe">Click Me</button> -->
            <form id="continueButton" name="form1" method="POST" action="task1.php">
                <input id="sessionField" type="hidden" name="wlux_session" value="0" />
                <input id="continueBtn" type="submit" value="continue" disabled />
            </form>
			<script type="text/javascript">

            </script>
        </div> <!-- container -->
    </body>
</html>
