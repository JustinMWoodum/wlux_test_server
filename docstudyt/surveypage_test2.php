<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
	
	$argName = 'studyId';
	$studyId = 0;
	if (in_array($argName, $postData)) {
		$studyId = $postData[$argName];
	}
	$argName = 'sessionId';
	$sessionId = 0;
	if (in_array($argName, $postData)) {
		$sessionId = $postData[$argName];
	}
	$argName = 'conditionId';
	$conditionId = 0;
	if (in_array($argName, $postData)) {
		$conditionId = $postData[$argName];
	}
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Embedded study test page 2</title>
</head>

<body>
<div id="test">MessageTest</div>
<script type="text/javascript">
	// for compatibility when testing
	var sessionInfo = [];
	sessionInfo.studyId =  <?php echo $studyId ?>;
	sessionInfo.sessionId =  <?php echo $sessionId ?>;
	sessionInfo.conditionId =  <?php echo $conditionId ?>;	
</script>
<!-- begin stuff to extract for the study config -->
<script type="text/javascript">		
function listener(event){
  if ( event.origin !== "http://www.surveygizmo.com" ) {
	return;
  }	  
  if ("surveyStarted" == event.data) {
	  // hide the continue button
	  var d = document.getElementById("continueFormDiv");
	  d.style.display = "none";
  } else if ("surveyComplete" == event.data) {
	  // hide the continue button
	  var i = document.getElementById("surveyFrame");
	  i.height = "400";
	  var d = document.getElementById("continueFormDiv");
	  d.style.display = "block";
  } else {
	  // do nothing
  }
}
</script>
<p>Please answer the questions below and then press continue.</p>
<div id="surveyDiv" style="margin-left:auto; margin-right:auto; width:700px;"> </div>
<script type="text/javascript">
	document.getElementById("surveyDiv").innerHTML = "<iframe id=\"surveyFrame\" src=\"http://www.surveygizmo.com/s3/1350906/pretest?studyid=" + sessionInfo.studyId + "&conditionid=" + sessionInfo.conditionId + "&sessionid=" + sessionInfo.sessionId + "\" frameborder=\"0\" width=\"700\" height=\"2400\" ></iframe>";	
	if (window.addEventListener){
	  addEventListener("message", listener, false);
	} else {
	  attachEvent("onmessage", listener);
	}
</script>
<!-- begin stuff to extract for the study config -->
<div id="continueFormDiv">
<button onclick="alert('Thanks!')">Continue to the next task</button>
</div>
</body>
</html>