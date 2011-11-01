<?php
include("user.php");
function print_messages($user_id, $act="inbox") {
	
	if($act=="inbox") {
		$result = pg_query("SELECT * FROM messages WHERE to_id={$user_id} ORDER BY uid DESC") or die(pg_last_error());
		$i=0;
		while ($messages = pg_fetch_array($result))
		{
		if(isset($_GET['page'])) $page=$_GET['page']*10; else $page=10;
			if($i>=$page-10 and $i<$page) {
			if($messages['read_status']=="u") {$message_style="unread_message";} else {$message_style="message";}
			$user=user_array($messages['from_id']);
			$message_id=$messages['message_id_to'];
			echo "<a href=\"?act=show&id=".$message_id."\" class=\"message_link\">";
			echo "<div class=\"{$message_style}\">";
			echo "<table><tr><td width=\"50\">";
			if($user['avatar']!="nothing") echo "<img src=\"./i/{$messages['from_id']}/{$user['avatar']}\">";
			echo "</td><td>{$user['name']} {$user['lastname']}<br>{$messages['message']}</td></tr></table>";
			//echo "<a href=\"?delete={$message_id};\">{$lang['delete']}</a>";
			echo "</div>";
			echo "</a>";
			}
		$i++;
		}
	}
	
	if($act=="outbox") {
		$result = pg_query("SELECT * FROM messages WHERE from_id={$user_id} ORDER BY uid DESC") or die(pg_last_error());
		while ($messages = pg_fetch_array($result)) 
		{
			if(isset($_GET['page'])) $page=$_GET['page']*10; else $page=10;
			if($i>=$page-10 and $i<$page)
			{
				$user=user_array($messages['to_id']);
				echo "<a href=\"?act=show&id=".$messages['message_id_from']."&out\">";
				echo "<div class=\"message\">";
				echo "<table><tr><td width=\"50\">";
				if($user['avatar']!="nothing") echo "<img src=\"./i/{$messages['to_id']}/{$user['avatar']}\">";
				echo "</td><td>{$user['name']} {$user['lastname']}<br>{$messages['message']}</td></tr></table>";
				echo "</div></a>";
			}
			$i++;
		}
	}
}
function message($message)
{
	$str=array("\"","'","<",">");
	$to_str=array("&quot;","&rsquo;","&lt;","&gt;");
	$replace_message=trim(str_replace($str,$to_str,$message));
	return preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#",'<a href="\\0">\\0</a>',$replace_message);
}
?>
