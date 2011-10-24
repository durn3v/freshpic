<?php
session_start();
include("../../config.php");
include("../../includes/mail.php");
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
	if($_GET['act']=='inbox')
	{
		$db->connect();
		print_messages($_SESSION['user_id']);
		$db->close();
	}

	if($_GET['act']=='outbox')
	{
		$db->connect();
		print_messages($_SESSION['user_id'], 'outbox');
		$db->close();
	}
}
?>
