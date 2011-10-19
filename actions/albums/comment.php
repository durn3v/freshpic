<?php
session_start();
include("../../config.php");

if(isset($_SESSION['user_id']))
{
	$message=message($_GET['comment']);
	$db->connect();
	$db->action("INSERT INTO photo_comments (user_id,user_photo,image,comment) VALUES ({$_SESSION['user_id']},{$_GET['user_photo']}, '{$_GET['image']}','{$message}')");
	$db->action("SELECT * FROM photo_comments WHERE user_photo={$_GET['user_photo']} AND image='{$_GET['image']}' ORDER BY uid");
	if(pg_num_rows($db->result)!=0)
	{
	echo "<table>";
		while($comment=pg_fetch_array($db->result))
		{
			$user=user_array($comment['user_id']);
			echo "<tr><td><img src=\"i/{$comment['user_id']}/{$user['avatar']}.jpg\"></td>
			<td>{$user['name']} {$user['lastname']}
			<br>{$comment['comment']}</td></tr>";
		}
	echo "</table>";
	}
}
$db->close();
?>
