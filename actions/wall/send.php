<?php
session_start();
include("../../config.php");
function message($message)
{
	$str=array("\"","'","<",">");
	$to_str=array("&quot;","&rsquo;","&lt;","&gt;");
	$replace_message=str_replace($str,$to_str,$message);
	return preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#",'<a href="\\0">\\0</a>',$replace_message);
}
if(isset($_SESSION['user_id']))
{
	$message=message($_POST['message']);
	$db->connect();
	$db->action("INSERT INTO wall (user_id,message) VALUES ({$_SESSION['user_id']},'{$message}')");
	$db->close();
}
?>
