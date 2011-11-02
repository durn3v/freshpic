<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);

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
echo 	"<form method=\"POST\" action=\"home.php\">
	E-mail: <input type=\"text\" name=\"email\">
	{$lang['password']}: <input type=\"password\" name=\"pass\">
	<input type=\"submit\" value=\"sign in\">
	{$lang['remember_me']}<input type=\"checkbox\" name=\"remember\" value=\"yes\" checked>
        </form>";
echo "</div>";
echo "<div class=\"main\">";
echo "<a href=\"http://api.vkontakte.ru/oauth/authorize?client_id=2661341&scope=friends,photos&redirect_uri=freshpic.org&response_type=code\" target=\"_blank\">Тестовая авторизация</a>";

//	echo "<br><a href=\"https://api.vkontakte.ru/oauth/access_token?client_id=2661341&client_secret=THISIS&code={$_GET['code']}\">next</a>";


/* --VK AUTH. TEST-- */
if(isset($_GET['code']))
{
	$vk_code=$_GET['code'];
	if(!isset($_COOKIE['vk_token']))
	{
		$out = my_curl('https://api.vkontakte.ru/oauth/access_token?client_id=2661341&client_secret=THISIS&code='.$vk_code);
		$json_data=(array)json_decode($out);
		setcookie("vk_token", $json_data['access_token'], time()+24*30, "/");
		setcookie("vk_user", $json_data['user_id'], time()+24*30, "/");
	}
}
	//print_r($json_data);
	echo "<br>";
	//echo $json_data["access_token"];
	$for_curl="https://api.vkontakte.ru/method/friends.get?uid=".$_COOKIE['vk_user']."&access_token=".$_COOKIE['vk_token'];
	$out=my_curl($for_curl);
	$json_user = (array)json_decode($out);
	$array_user=(array)$json_user['response'];
	$user=(array)$array_user['0'];
	//print_r($array_user);
	echo "<br>";
	//print_r($user);
	foreach($array_user as $value)
	{
		$for_curl="https://api.vkontakte.ru/method/getProfiles?uid=".$value."&access_token=".$_COOKIE['vk_token'];
		$out=my_curl($for_curl);
		$json_user = (array)json_decode($out);
		$array_user=(array)$json_user['response'];
		$user=(array)$array_user['0'];
		echo $user['first_name']." ".$user['last_name']."<br>";
	}
	//echo "<br>NAME:".$user['first_name']." LASTNAME:".$user['last_name'];
function my_curl($link) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	return curl_exec($curl);
	curl_close($curl);
}

/* --END-- */

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
