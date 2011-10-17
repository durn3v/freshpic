<?php
session_start();
include("../../config.php");
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
	echo "<a href=\"#!{$next_image}\"><img src=\"/s/{$user_id}/{$name}.jpg\"></a><br>
	<input onclick=\"like()\" type=\"button\" value=\"like\"> <input onclick=\"dislike()\" type=\"button\" value=\"dislike\">
	<br><div id=\"like_inf\">like:{$like} dislike:{$dislike}</div>";
	echo "<input type=\"text\" id=\"comment\"><input onclick=\"comment()\" type=\"button\" value=\"send\">";
	echo "<div id=\"comments\">";
	$db->action("SELECT * FROM photo_comments WHERE user_photo={$user_id} AND image='{$name}' ORDER BY uid DESC");
	if(pg_num_rows($db->result)!=0)
	{
		while($comment=pg_fetch_array($db->result))
		{
			echo "<p>{$comment['user_id']}<br>{$comment['comment']}</p>";
		}
	}
	echo "</div>";
}
$db->close();
?>