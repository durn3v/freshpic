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
if(!function_exists("get_avatar_small"))
{
	function get_avatar_small($uid)
	{
		$result=pg_query("SELECT avatar FROM users WHERE uid={$uid}") or die(pg_last_error());
		while($user=pg_fetch_array($result))
		{
			$avatar=$user['avatar'];
		}
		if($avatar=="nothing")
		{
			return "<img src=\"images/nothing_small.jpg\" style=\"margin-left:1px;\">";
		} else {
			return "<img src=\"i/$uid/$avatar.jpg\" style=\"margin-left:1px;\">";
		}
	}
}
?>
