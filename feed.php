<?php
session_start();
include("config.php");

function user($from_id) {
	$sql_from="SELECT * FROM users WHERE uid=".$from_id;
	$result_user=pg_query($sql_from) or die(pg_last_error());
	while ($from_user = pg_fetch_array($result_user)) { return $from_user['name']." ".$from_user['lastname']; }
}
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
	$db->action("SELECT * FROM followers WHERE who={$_SESSION['user_id']}");
	$i=0;
	while($follow=pg_fetch_array($db->result))
	{
		$following[]=$follow['whom'];
		$i++;
	}
	for($x=1; $x<=$i; $x++)
	{
		$y=$x-1;
		$users=$users."user_id={$following[$y]}";
		if($x!=$i) $users=$users." AND ";
	}
	$db->action("SELECT * FROM feed WHERE {$users} ORDER BY uid DESC");
	if(pg_num_rows($db->result)!=0)
	{
		while($feed=pg_fetch_array($db->result))
		{
			$name=user($feed['user_id']);
			if($feed['type']=='photos') 
			{
			$album=album($feed['user_id'],$feed['value2']); 
			echo "{$name} added {$feed['value1']} photos to album <a href=\"albums.php?user={$feed['user_id']}&album={$feed['value2']}\">{$album}</a>";
			}
			if($feed['type']=='status') 
			{
			echo "{$name}: {$feed['value1']}";
			}
			echo "<br>";
		}
	}
	$db->close();
echo $close;
}
?>
