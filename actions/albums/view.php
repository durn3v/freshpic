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
	<input onclick=\"like()\" type=\"button\" value=\"like\">|<input onclick=\"dislike()\" type=\"button\" value=\"dislike\">
	<br>like:{$like} dislike:{$dislike}";
}
?>