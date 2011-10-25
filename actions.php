<?php
ini_set('display_errors',true);
ini_set('html_errors',true);
ini_set('error_reporting',E_ALL ^ E_NOTICE);
session_start();
include_once("config.php");
$ajax=$_POST['ajax'];
switch($ajax) {
	case "wall":
		include_once("includes/wall.php");
		$db->connect();
		print_wall($_SESSION['user_id']);
		$db->free();
		$db->close();
	break;
}
?>
