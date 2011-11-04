<?php
//ini_set("display_errors","1");
//ini_set("display_startup_errors","1");
//ini_set('error_reporting', E_ALL);

session_start();
include_once("config.php");

if(isset($_SESSION['user_id']))
{
	header("Location: home.php");
	exit();
}
echo "<html><head><meta http-equiv=\"Content-type\" content=\"test/html; charset=utf-8\"><title>Welcome!</title>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" /></head>";
echo "<div class=\"top\">";
echo 	"<form method=\"POST\" action=\"login.php\">
	E-mail: <input type=\"text\" name=\"email\">
	{$lang['password']}: <input type=\"password\" name=\"pass\">
	<input type=\"submit\" value=\"sign in\">
	{$lang['remember_me']}<input type=\"checkbox\" name=\"remember\" value=\"yes\" checked>
        </form>";
echo "</div>";
echo "<div class=\"main\">";
echo "<a href=\"http://api.vkontakte.ru/oauth/authorize?client_id=2661341&scope=friends,photo&redirect_uri=freshpic.org/login.php&response_type=code\" target=\"_blank\">Тестовая авторизация</a>";

echo 	"<form method=\"POST\" action=\"join.php\">
	<table style=\"text-align:right;\"><tr><td colspan=\"2\" align=\"center\"><b>
	{$lang['registration']}
	</b></td></tr><tr><td>
	{$lang['firstname']}:</td><td><input type=\"text\" name=\"firstname\"></td></tr><tr><td>
	{$lang['lastname']}:</td><td><input type=\"text\" name=\"lastname\"></td></tr><tr><td>
	{$lang['email']}:</td><td><input type=\"text\" name=\"email\"></td></tr><tr><td>
	{$lang['password']}:</td><td><input type=\"password\" name=\"password\"></td></tr><tr><td>
	{$lang['ver_password']}:</td><td><input type=\"password\" name=\"ver_password\"></td></tr><tr><td>
	{$lang['sex']}: {$lang['sex_m']}<input type=\"radio\" name=\"sex\" value=\"m\">
	{$lang['sex_f']}<input type=\"radio\" name=\"sex\" value=\"f\"></td></tr>
	<tr><td colspan=\"2\">
	<input type=\"hidden\" name=\"act\">
	<input type=\"submit\" value=\"{$lang['registration']}\">
	</form></td></tr></table>";



echo "<div class=\"lang\">
	<a href=\"&lang=ru\">Русский</a>
	<a href=\"&lang=en\">English</a>
	<a href=\"&lang=de\">Deutsch</a></div>
	</div></div></body></html>";
?>
