<?php
session_start();
if(isset($_SESSION['user_id']))
{
	header("Location: home.php");
	exit();
}
include("config.php");
echo "<html><head><meta http-equiv=\"Content-type\" content=\"test/html; charset=utf-8\">";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" />";
echo "<div class=\"top\">";
echo 	"<form method=\"POST\" action=\"home.php\">
	E-mail: <input type=\"text\" name=\"email\">
	{$lang['password']}: <input type=\"password\" name=\"pass\">
	<input type=\"submit\" value=\"sign in\">
	{$lang['remember_me']}<input type=\"checkbox\" name=\"remember\" value=\"yes\" checked>
        </form>";
echo "</div>";
echo "<div class=\"main\">";


echo 	"<form method=\"POST\" action=\"join.php\">
	<table style=\"text-align:right;\"><tr><td collspan=\"2\"><b>
	{$lang['registration']}
	</b></td></tr><tr><td>
	{$lang['firstname']}:</td><td><input type=\"text\" name=\"firstname\"></td></tr><tr><td>
	{$lang['lastname']}:</td><td><input type=\"text\" name=\"lastname\"></td></tr><tr><td>
	{$lang['email']}:</td><td><input type=\"text\" name=\"email\"></td></tr><tr><td>
	{$lang['password']}:</td><td><input type=\"password\" name=\"password\"></td></tr><tr><td>
	{$lang['ver_password']}:</td><td><input type=\"password\" name=\"ver_password\"></td></tr><tr><td>
	{$lang['sex']}: {$lang['sex_m']}<input type=\"radio\" name=\"sex\" value=\"m\">
	{$lang['sex_f']}<input type=\"radio\" name=\"sex\" value=\"f\"></td></tr>
	<tr><td collspan=\"2\">
	<input type=\"hidden\" name=\"act\">
	<input type=\"submit\" value=\"{$lang['registration']}\">
	</form></td></tr></table>";

echo "<div class=\"lang\">
	<a href=\"&lang=ru\">Русский</a>
	<a href=\"&lang=en\">English</a>
	<a href=\"&lang=de\">Deutsch</a></div>
	</div></div></body></html>";
?>
