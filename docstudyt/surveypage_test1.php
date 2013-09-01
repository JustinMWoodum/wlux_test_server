<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Embedded study test page</title>
</head>

<body>
<div id="inputDiv">
<p>
  <label>Study ID:</label>
  <br />
  <input id="studyIdInput" type="text" placeholder="Enter the study ID" value="2599" />
</p>
<p>
  <label>Session ID:</label>
  <br />
  <input id="sessionIdInput" type="text" placeholder="Enter the session ID" />
  <button title="Use the current time" style="width: 10.0em" onclick="{var d=new Date(); document.getElementById('sessionIdInput').value = d.getTime().toString();}" >Use current time</button>
</p>
<p>
  <label>Condition ID:</label>
  <br />
  <input id="conditionIdInput" type="text" placeholder="Enter the condition ID" value="1" />
</p>
<p>
  <button title="Call the survey" style="width:10.0em" onclick="showSurvey (document.getElementById('studyIdInput').value, document.getElementById('sessionIdInput').value, document.getElementById('conditionIdInput').value)">Go</button>
</p>
<div id="surveyDiv"> 
<iframe src="http://www.surveygizmo.com/s3/1350906/pretest?studyid=2599&conditionid=2&sessionid=323456789" frameborder="0" width="700" height="500" ></iframe></div>
</body>
</html>