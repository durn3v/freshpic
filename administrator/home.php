<?php
session_start();
if($_SESSION['admin']=="admin")
{
	echo "<html><head><meta http-equiv=\"Content-type\" content=\"test/html; charset=utf-8\"><title>Admin</title>";
	echo "панель администратирования<br>";
	echo "<a href=\"http://freshpic.org/phppgadmin/\" target=\"_blank\">Postgres</a>";
	echo "</html>";
}
?>
