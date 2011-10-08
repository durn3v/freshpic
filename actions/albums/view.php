<?php
session_start();
include("../../config.php");
if(isset($_SESSION['user_id']))
{
	$name=$_GET['image'];
	echo "<img src=\"../../s/{$name}\">";
}
?>