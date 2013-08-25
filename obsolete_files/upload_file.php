<?php
require 'config_files.php';
if ($_FILES["file"]["error"] > 0){
	echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {
    move_uploaded_file($_FILES["file"]["tmp_name"], $CONFIG_FILE_PATH.$CONFIG_FILE_NAME);
    header("Location: study-configuration.php");
}
?>