<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
session_start();
include_once("config.php");
echo $start;
echo "<title>Home</title>";
echo $after_title;
echo $after_scripts;
if(isset($_SESSION['user_id']))
{
	if(isset($_GET['q']))
	{
		$db->connect();
		$db->action("SELECT * FROM users WHERE name LIKE '%{$_GET['q']}%' or lastname='%{$_GET['q']}%'");
		while($result=pg_fetch_array($db->result))
		{
			echo "<a href=\"{$result['uid']}\"><img src=\"i/{$result['uid']}/{$result['avatar']}\">{$result['name']} {$result['lastname']}</a>";
		}
		$db->close();
	}
}
echo $close;
?>
