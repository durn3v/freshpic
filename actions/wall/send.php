<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
ini_set('html_errors',true);
ini_set('error_reporting',E_ALL ^ E_NOTICE);
session_start();
include_once("../../config.php");
include_once("../../includes/wall.php");
if(isset($_SESSION['user_id']))
{
	$db->connect();
	send_status($_POST['message']);
	$db->close();
}
?>
