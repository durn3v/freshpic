<?php
session_start();
include("../../config.php");
function from_user($from_id) {
$sql_from="SELECT * FROM users WHERE uid=".$from_id;
$result_user=pg_query($sql_from) or die(pg_last_error());
while ($from_user = pg_fetch_array($result_user)) { return $from_user['name']." ".$from_user['lastname']; }
}
function avatar($uid) {
$sql="SELECT avatar FROM users WHERE uid={$uid}";
$result=pg_query($sql) or die(pg_last_error());
while($users=pg_fetch_array($result)) 
	{return $users['avatar'];}
}
if(isset($_SESSION['user_id']))
{
	$db->connect();
	$db->action("SELECT * FROM messages WHERE to_id='".$_SESSION['user_id']."' ORDER BY uid DESC LIMIT 10");
	$i=0;
	while ($messages = pg_fetch_array($db->result))
	{
	if(isset($_GET['page'])) $page=$_GET['page']*10; else $page=10;
		if($i>=$page-10 and $i<$page)
		{
			if($messages['read_status']=="u") {$message_style="unread_message";} else {$message_style="message";}
			$user=from_user($messages['from_id']);
			$message_id=$messages['message_id_to'];
			echo "<a href=\"?act=show&id=".$message_id."\" class=\"message_link\">";
			echo "<div class=\"{$message_style}\">";
			echo "<table><tr><td width=\"50\">";
			$avatar=avatar($messages['from_id']);
			if($avatar!="nothing") echo "<img src=\"./i/{$avatar}\">";
			echo "</td><td>{$user}<br>{$messages['message']}</td></tr></table>";
			//echo "<a href=\"?delete={$message_id};\">{$lang['delete']}</a>";
			echo "</div>";
			echo "</a>";
		}
	$i++;
	}
	$db->close();
}
?>
