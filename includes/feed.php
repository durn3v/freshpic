<?php
function get_feed() {
$result=pg_query("SELECT * FROM followers WHERE who={$_SESSION['user_id']}");
	$i=0;
	while($follow=pg_fetch_array($result))
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
	$result=pg_query("SELECT * FROM feed WHERE {$users} ORDER BY uid DESC LIMIT 10");
	if(pg_num_rows($result)!=0)
	{
	echo "<table>";
		while($feed=pg_fetch_array($result))
		{
			$user=user_array($feed['user_id']);
			if($feed['type']=='photos') 
			{
			$album=album($feed['user_id'],$feed['value2']); 
			echo "<tr><td><a href=\"{$feed['user_id']}\"><img src=\"i/{$feed['user_id']}/{$user['avatar']}.jpg\"></a></td><td>{$user['name']} {$user['lastname']}<br>added {$feed['value1']} photos to album <a href=\"albums.php?user={$feed['user_id']}&album={$feed['value2']}\">{$album}</a></td></tr>";
			}
			if($feed['type']=='status') 
			{
			echo "<tr><td><a href=\"{$feed['user_id']}\"><img src=\"i/{$feed['user_id']}/{$user['avatar']}.jpg\"></a></td><td><p>{$user['name']} {$user['lastname']}</p>{$feed['value1']}</td></tr>";
			}
		}
	echo "</table>";
	}
}
?>
