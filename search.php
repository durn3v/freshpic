<?php
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
		$q=explode(" ",$_GET['p'])
		$db->connect();
		foreach($q as $search) {
			$db->action("SELECT * FROM users WHERE name LIKE '%' OR lastname LIKE ''");
		}
		$db->close();
	}
}
echo $close;
?>
