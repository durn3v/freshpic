<?php
session_start();
include("../../config.php");
if(isset($_SESSION['user_id']))
{
	$db->connect();
	$db->action("SELECT * FROM wall WHERE user_id={$_SESSION['user_id']} ORDER BY uid DESC");
	if(pg_num_rows($db->result)!=0)
	{
		while($wall=pg_fetch_array($db->result))
		{
			echo "<div id=\"wall\">{$wall['message']}<br></div>";
		}
	}
	$db->close();
}
?>