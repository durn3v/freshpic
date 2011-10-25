<?php
include_once("user.php");
function print_wall($user_id) {
	$result = pg_query("SELECT * FROM wall WHERE user_id={$user_id} ORDER BY uid DESC LIMIT 10") or die(pg_last_error());
	$user=user_array($user_id);
		if(pg_num_rows($result)!=0)
		{
			$i=0;
			while($wall=pg_fetch_array($result))
			{
				if($i==0){
				 	$wall['message']="<b>{$wall['message']}</b>";
				}
				echo "<tr id=\"wall_message\"><td><img src=\"i/{$user_id}/{$user['avatar']}.jpg\"></td><td>{$b}{$wall['message']}</td></tr>";
				$i++;
			}
		}
}
function send_status($mess) {
	require_once("mail.php");
	$message=message($mess);
	pg_query("INSERT INTO wall (user_id,message) VALUES ({$_SESSION['user_id']},'{$message}');") or die(pg_last_error());
	pg_query("INSERT INTO feed (user_id,type,value1) VALUES ({$_SESSION['user_id']}, 'status', '{$message}');") or die(pg_last_error());
}
?>
