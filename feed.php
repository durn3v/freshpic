<?php
session_start();
include_once("config.php");
include_once("includes/user.php");
include_once("includes/feed.php");

function album($id,$album) {
	$sql="SELECT * FROM albums WHERE user_id={$id} AND album_id={$album}";
	$result=pg_query($sql) or die(pg_last_error());
	while ($album_name = pg_fetch_array($result)) { return $album_name['name']; }
}

if(isset($_SESSION['user_id']))
{
echo $start;
echo "<title>{$lang['feed']}</title>";
echo $after_title;
echo "<script>var js_title='{$lang['feed']}';</script>";
echo $after_scripts;
	$db->connect();
	get_feed();
	$db->close();
echo $close;
}
?>
