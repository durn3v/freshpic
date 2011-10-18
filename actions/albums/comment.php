<?php
session_start();
include("../../config.php");
function user($from_id) 
{
	$sql_from="SELECT * FROM users WHERE uid=".$from_id;
	$result_user=pg_query($sql_from) or die(pg_last_error());
	while ($from_user = pg_fetch_array($result_user)) { return $from_user['name']." ".$from_user['lastname']; }
}
function message($message)
{
	$str=array("\"","'","<",">");
	$to_str=array("&quot;","&rsquo;","&lt;","&gt;");
	$replace_message=trim(str_replace($str,$to_str,$message));
	return preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#",'<a href="\\0">\\0</a>',$replace_message);
}
if(isset($_SESSION['user_id']))
{
	$message=message($_GET['comment']);
	$db->connect();
	$db->action("INSERT INTO photo_comments (user_id,user_photo,image,comment) VALUES ({$_SESSION['user_id']},{$_GET['user_photo']}, '{$_GET['image']}','{$message}')");
	$db->action("SELECT * FROM photo_comments WHERE user_photo={$_SESSION['user_id']} AND image='{$_GET['image']}' ORDER BY uid");
	if(pg_num_rows($db->result)!=0)
	{
		while($comment=pg_fetch_array($db->result))
		{
			$user=user($comment['user_id']);
			echo "<p>{$user}<br>{$comment['comment']}</p>";
		}
	}
}
$db->close();
?>
