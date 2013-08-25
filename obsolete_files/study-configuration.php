<?php 
//This file submits information to webservice to generate a study page
include 'config_files.php';
error_reporting(E_STRICT);
?>
	<html>
	<head>
		<title></title>
		<script src="jquery.js"></script>
		<script src="study-configuration.js"></script>
        <style type="text/css">
			span.hinttext {
				color:#AAA;
				font-family:Tahoma, Geneva, sans-serif;
				font-size:small;
			}
			dt {padding-top:0.5ex;}
		</style>
	</head>
	<body style="font-family:Verdana, Geneva, sans-serif; font-size:medium">
		<form>
			<fieldset>
			<?php 
				$file = $CONFIG_FILE_PATH.$CONFIG_FILE_NAME;
				if (file_exists($file)) {
					$defaults = unserialize(file_get_contents($file));
					//echo($defaults);
				}
				?>
                <h1>WebLabUX Study Configuration Simulator</h1>
					<!--
					If a config file exists on the server this form will load with the configuration
					already in the fields. If not then the fields will be blank
					-->
					<dl>
                    <dt>Condition ID:&nbsp;<input id="conditionId" style="width:2em" type="number" name="conditionId" value=<?php if($defaults){ print trim($defaults['conditionId']); } ?> required><span class="hinttext">Enter a number between 1 and 4 (incl).</span></dt>
					<dt>CSS URL:&nbsp;<span class="hinttext">The CSS file to use for this condition specified in the Condition ID field</span></dt><dd><input id="cssURL" type="text" style="width:40em" name="cssURL" placeholder=".css url" value=<?php if($defaults){ print trim($defaults['cssURL']); }?> required><dd>
					<dt>Task bar CSS:&nbsp;<span class="hinttext">The CSS file to use for the task tab. Usually:<i>http://wlux.uw.edu/rbwatson/wluxTaskBar.css</i></span></dt><dd><input id="taskBarCSS" type="url" style="width:40em" name="taskBarCSS" placeholder=".css url" value=<?php if($defaults){ print trim($defaults['taskBarCSS']); }?> required><dd>
					<dt>Button Text:&nbsp;<span class="hinttext">The text that appears on the button that ends the task. Usually: <i>End task</i> or <i>End study</i>.</span></dt><dd><input id="buttonText" type="text" style="width:40em" name="buttonText" value=<?php if($defaults){ print trim($defaults['buttonText']); }?> required><dd>
					<dt>Return URL:&nbsp;<span class="hinttext">The URL of the page to return to when then end-task button is clicked. Usually: <i>http://wlux.uw.edu/rbwatson/end.php</i></span></dt><dd><input id="returnURL" type="url" style="width:40em" name="returnURL" value=<?php if($defaults){ print trim($defaults['returnURL']); }?> required></dd>
					<dt>Task Text:&nbsp;<span class="hinttext">Plain text to display in the task info bar.</span></dt><dd><textarea id="taskText" style="vertical-align:top" name="taskText" id="taskText" cols="60" rows="3" ><?php if($defaults){ print trim($defaults['taskText']); }?> </textarea><dd>
					<dt>Task HTML:&nbsp;<span class="hinttext">HTML formatted for the task info bar. Note that if there are any characters in this field, the text in the Task Text box is ignored.</span></dt><dd><textarea id="taskHTML" style="vertical-align:top" name="taskHTML" id="taskHTML" cols="60" rows="6" ><?php if($defaults){ print trim($defaults['taskHTML']); }?></textarea></dd>
					<dt>Show Tab Text:&nbsp;<span class="hinttext">The text in the tab that shows a hidden task info bar.</span></dt><dd><input id="tabShowText" type="text" name="tabShowText" value=<?php if($defaults){ print trim($defaults['tabShowText']); }?> required></dd>
					<dt>Hide Tab Text:&nbsp;<span class="hinttext">The text in the tab that hides a visible task info bar.</span></dt><dd><input id="tabHideText" type="text" name="tabHideText" value=<?php if($defaults){ print trim($defaults['tabHideText']); }?> required></dd>
					<dt>Automaticly change Condition ID:<input type="checkbox" id="autoconditionid" name="autocondditionid" value="1">&nbsp;<span class="hinttext">Check this box to automatically change the Condition ID and CSS URL values.</span></dt>
                    </dl>
					<p><input id="submitbtn" type="button" value="Submit this info to the server">&nbsp;<span class="hinttext"><a href="https://github.com/rbwatson/wlux_test_server/blob/master/readme.md#study-config-data-object" target="_blank">Learn more about these fields</a></span></p>				
			</fieldset>
		</form>
		<form action="upload_file.php" method="post" enctype="multipart/form-data">
			<label for="file">Filename:</label>
			<input type="file" name="file" id="file"><br>
			<input type="submit" name="submit" value="Submit">
		</form>
		<a href="<?php echo $file ?>">Right click to download configuration</a>
	</body>
	</html>
