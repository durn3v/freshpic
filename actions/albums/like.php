<?php
session_start();
include("../../config.php");
if(isset($_SESSION['user_id']))
{
	$db->connect();
	$db->action("SELECT * FROM vote WHERE user_id={$_SESSION['user_id']} AND image='{$_GET['name']}';");
	if(pg_num_rows($db->result)==0)
	{
	
		if($_GET['act']=='like')
		{
			$db->action("UPDATE images SET \"like\"=\"like\"+1 WHERE user_id={$_GET['user_id']} AND name='{$_GET['name']}'");
		}
		if($_GET['act']=='dislike')
		{
			$db->action("UPDATE images SET dislike=dislike+1 WHERE user_id={$_GET['user_id']} AND name='{$_GET['name']}'");
		}
		
		$db->action("SELECT * FROM images WHERE user_id={$_GET['user_id']} AND name='{$_GET['name']}';");
		while($image=pg_fetch_array($db->result))
		{
			$like=$image['like'];
			$dislike=$image['dislike'];
		}

		$db->action("INSERT INTO vote (user_id,image) VALUES ({$_SESSION['user_id']}, '{$_GET['name']}');");
		echo "like:{$like} dislike:{$dislike}";
	}
	$db->close();
}
?>
