<?php
session_start();
include_once("../../config.php");
include_once("../../includes/user.php");

if(isset($_SESSION['user_id']))
{
	$name=$_POST['image'];
	$user_id=$_POST['user'];
	$db->connect();
	$db->action("SELECT * FROM images WHERE user_id={$user_id} AND name='{$name}'");
	while($image=pg_fetch_array($db->result))
	{
		$album_id=$image['album_id'];
		$seq=$image['seq'];
		$like=$image['like'];
		$dislike=$image['dislike'];
	}
	$next=$seq+1;
	$db->action("SELECT * FROM images WHERE album_id={$album_id} AND seq={$next}");
	if(pg_num_rows($db->result)==0)
	{
		$db->action("SELECT * FROM images WHERE album_id={$album_id} AND seq=1");
		while($image=pg_fetch_array($db->result))
		{
			$next_image=$image['name'];
		}
	} else {
		while($image=pg_fetch_array($db->result))
		{
			$next_image=$image['name'];
		}
	}
	echo "<a href=\"#!{$next_image}\" onclick=\"if(navigator.userAgent.toLowerCase().indexOf('firefox/3.5')!=-1) view2('{$next_image}')\">
	<img src=\"/s/{$user_id}/{$name}.jpg\"></a><br>
	<input onclick=\"like()\" type=\"button\" value=\"like\"> <input onclick=\"dislike()\" type=\"button\" value=\"dislike\">
	<br><div id=\"like_inf\">like:{$like} dislike:{$dislike}</div>";
	echo "<div id=\"comments\" style=\"text-align:left; max-width:500px; margin: 0 auto;\">";
	$db->action("SELECT * FROM photo_comments WHERE user_photo={$user_id} AND image='{$name}' ORDER BY uid");
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
	echo "</div>";
	echo "<textarea id=\"comment\" cols=\"30\"></textarea><br><input onclick=\"comment()\" type=\"button\" value=\"send\">";
}
$db->close();
?>
