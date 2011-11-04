<?php
session_start();
include_once("config.php");
if(isset($_POST['login']) and isset($_POST['password']))
{
	if($_POST['login']==ADMIN_LOGIN and $_POST['password']==ADMIN_PASSWORD)
	{
		$_SESSION['admin']="admin";
		header("Location: home.php");
		exit;
	}
}
?>
