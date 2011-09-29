<?php
session_start();
include("../../config.php");
if(isset($_SESSION['user_id']))
{
	$db->connect();;
	$db->action("UPDATE users SET online_time='".time()."' WHERE uid='{$_SESSION['user_id']}'");
	$db->close();
}
?>
