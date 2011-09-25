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
$db->action("SELECT * FROM counts WHERE user_id=".$_SESSION['user_id']);
while($count=pg_fetch_array($db->result)) {
	$message_id_from=$count['inbox']+$count['outbox']+1;
}
$db->action("SELECT * FROM counts WHERE user_id=".$_POST['to']);
while($count=pg_fetch_array($db->result)) {
	$message_id_to=$count['inbox']+$count['outbox']+1;
}
$db->action("INSERT INTO messages 
		(from_id,to_id,subject,message,read_status,message_id_from,message_id_to)
		VALUES
		(".$_SESSION['user_id'].",".$_POST['to'].",'".$_POST['subject']."','".$message."','u',".$message_id_from.",".$message_id_to.");");
		
$db->action("UPDATE counts SET outbox=outbox+1 WHERE user_id=".$_SESSION['user_id']);
$db->action("UPDATE counts SET inbox=inbox+1 WHERE user_id=".$_POST['to']);
$db->action("UPDATE counts SET new_messages=new_messages+1 WHERE user_id=".$_POST['to']);

$db->close();
}

?>
