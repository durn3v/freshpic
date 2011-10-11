<?php
session_start();
include("../../config.php");
if(isset($_SESSION['user_id']))
{
$db->connect();
	if($_GET['act']=='like')
	{
		$db->action("UPDATE images SET \"like\"=\"like\"+1 WHERE user_id={$_GET['user_id']} AND name='{$_GET['name']}'");
		echo "1";
	}
	if($_GET['act']=='dislike')
	{
		$db->action("UPDATE images SET dislike=dislike+1 WHERE user_id={$_GET['user_id']} AND name='{$_GET['name']}'");
		echo "2";
	}
$db->close();
}
?>
