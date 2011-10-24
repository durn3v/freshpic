<?php
session_start();
include("../../config.php");
include("../../includes/mail.php");
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
