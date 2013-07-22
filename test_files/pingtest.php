<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test page</title>
    <script type="text/javascript">
	  function updateValue (data)
	  {
		document.write('<p>Key = '+data.key+'</p>');
	  }
    </script>
  </head>
  <body onload="loadDiv2Fill()">
    <h1>Test page</h1>
    <div id="div2fill">
      <p>No text, yet...</p>
      <script type="text/javascript" src="http://wlux.uw.edu/rbwatson/ping.php?jsonp=updateValue&SessionID=numberFoundInQueryString"></script>
    </div>
  </body>
</html>