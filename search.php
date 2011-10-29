<?php
//ini_set("display_errors","1");
//ini_set("display_startup_errors","1");
//ini_set('error_reporting', E_ALL);
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
		$qlower=strtolower($_GET['q']);
		$q=explode(" ", $qlower);
		$db->connect();
		foreach($q as $value)
		{
			$max++;
		}
		for($i=1;$i<=$max;$i++)
		{
			$search=$search." lower(name) LIKE '%{$value}%' OR lower(lastname) LIKE '%{$value}%'";
			if($max!=1 and $i!=$max) $search = $search." OR";
		}
		$db->action("SELECT * FROM users WHERE {$search};");
		while($result=pg_fetch_array($db->result))
		{
			echo "<a href=\"{$result['uid']}\"><img src=\"i/{$result['uid']}/{$result['avatar']}\">{$result['name']} {$result['lastname']}</a>";
		}
		$db->close();
	}
}
echo $close;
?>
