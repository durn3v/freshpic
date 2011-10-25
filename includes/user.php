<?php
if(!function_exists("user_array"))
{
	function user_array($from_id) {
		$sql_from="SELECT * FROM users WHERE uid=".$from_id;
		$result_user=pg_query($sql_from) or die(pg_last_error());
		while ($from_user = pg_fetch_array($result_user)) 
		{ 
			return array('name' => $from_user['name'],'lastname' => $from_user['lastname'], 'avatar' => $from_user['avatar']); 
		}
	}
}
?>
