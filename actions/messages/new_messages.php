<?php
session_start();
include("../../config.php");

if(isset($_SESSION['user_id'])) 
{
	$db->connect();
	$db->action("SELECT new_messages FROM counts WHERE user_id=".$_SESSION['user_id']);
	while ($result = pg_fetch_array($db->result)) {
	$new=$result['new_messages'];
	
	}
		if($new==0) {
			echo "";
		} else {
			echo $new;
		}
	$db->close();
}
?>
