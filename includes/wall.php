<?php
function print_wall($user_id) {
	$result = pg_query("SELECT * FROM wall WHERE user_id={$user_id} ORDER BY uid DESC") or die(pg_last_error());
	$user=user_array($user_id);
		if(pg_num_rows($result)!=0)
		{
			while($wall=pg_fetch_array($result))
			{
				echo "<tr id=\"wall_message\"><td><img src=\"i/{$user_id}/{$user['avatar']}.jpg\"></td><td>{$wall['message']}</td></tr>";
			}
		}
}
?>
