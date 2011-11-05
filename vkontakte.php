<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
session_start();
include_once("config.php");
if(isset($_POST['email']) and isset($_POST['password']))
{
	$for_curl="https://api.vkontakte.ru/method/getProfiles?uid=".$_SESSION['vk_user_id']."&fields=city,country,photo_rec,photo_big,sex&access_token=".$_SESSION['vk_access_token'];
		$out=my_curl($for_curl);
		$json_user = (array)json_decode($out);
		$array_user=(array)$json_user['response'];
		$user=(array)$array_user['0'];
		if($user['country']!=0)
		{
			$for_curl="https://api.vkontakte.ru/method/places.getCountryById?cids={$user['country']}&access_token=".$_SESSION['vk_access_token'];
			$out=my_curl($for_curl);
			$json_country=(array)json_decode($out);
			$array_country=(array)$json_country['response'];
			$country=(array)$array_country['0'];
		}
		if($user['city']!=0)
		{
			$for_curl="https://api.vkontakte.ru/method/places.getCityById?cids={$user['city']}&access_token=".$_SESSION['vk_access_token'];
			$out=my_curl($for_curl);
			$json_city=(array)json_decode($out);
			$array_city=(array)$json_city['response'];
			$city=(array)$array_city['0'];
		}
	$db->connect();
	if($user['sex']==1) $sex="f"; elseif($user['sex']==2) $sex="m"; else $sex=" ";
	$password=md5($_POST['password']);
	$db->action("INSERT INTO users (email,pass,name,lastname,sex,country,city,avatar) VALUES ('{$_POST['email']}','{$password}','{$user['first_name']}','{$user['last_name']}','{$sex}','{$country['name']}','{$city['name']}','nothing');");
	$db->action("SELECT uid FROM users WHERE email='{$_POST['email']}';");
	while($result=pg_fetch_array($db->result))
	{
		$user_id=$result['uid'];
	}
	$db->action("INSERT INTO vk_users (vk_user_id,user_id) VALUES ({$_SESSION['vk_user_id']},{$user_id})");
	$db->action("INSERT INTO counts (user_id) VALUES ({$user_id})");
	$db->action("UPDATE counts SET images=1 WHERE user_id={$user_id}");
	$name = chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) )."1";
	mkdir("./i/{$user_id}");
	mkdir("./s/{$user_id}");
	mkdir("./p/{$user_id}");
	copy($user['photo_big'], "./s/{$user_id}/".$name.".jpg");
	copy($user['photo_rec'], "./i/{$user_id}/".$name.".jpg");
	$db->action("UPDATE users SET avatar='{$name}' WHERE uid={$user_id};");
	$db->close();
	unset($_SESSION['vk_user_id']);
	unset($_SESSION['vk_access_token']);
	$_SESSION['user_id']=$user_id;
	Header("Location: /{$user_id}");
	exit();
}
echo $start;
echo $after_title;
echo $after_scripts;
if(isset($_SESSION['vk_access_token']) and isset($_SESSION['vk_user_id']))
{
	$db->connect();
	$db->action("SELECT * FROM vk_users WHERE vk_user_id={$_SESSION['vk_user_id']}");
	if(pg_num_rows($db->result)==0) {
		$for_curl="https://api.vkontakte.ru/method/getProfiles?uid=".$_SESSION['vk_user_id']."&fields=city,country,photo_rec,photo_big,sex&access_token=".$_SESSION['vk_access_token'];
		$out=my_curl($for_curl);
		$json_user = (array)json_decode($out);
		$array_user=(array)$json_user['response'];
		$user=(array)$array_user['0'];
		if($user['country']!=0)
		{
			$for_curl="https://api.vkontakte.ru/method/places.getCountryById?cids={$user['country']}&access_token=".$_SESSION['vk_access_token'];
			$out=my_curl($for_curl);
			$json_country=(array)json_decode($out);
			$array_country=(array)$json_country['response'];
			$country=(array)$array_country['0'];
		}
		if($user['city']!=0)
		{
			$for_curl="https://api.vkontakte.ru/method/places.getCityById?cids={$user['city']}&access_token=".$_SESSION['vk_access_token'];
			$out=my_curl($for_curl);
			$json_city=(array)json_decode($out);
			$array_city=(array)$json_city['response'];
			$city=(array)$array_city['0'];
		}
		
		echo "<table><tr><td><img src=\"{$user['photo_rec']}\"></td><td>{$user['first_name']} {$user['last_name']}</td></tr>";
		if($user['country']!=0) echo "<tr><td colspan=\"2\">{$country['name']},{$city['name']}</td></tr>";
		echo "</table>";
		echo "Так как вы заходите в первый раз, введите ваш email и пароль, для того чтобы вы смогли авторизовываться с главной страницы сайта
			<form action=\"vkontakte.php\" method=\"POST\">
			<table>
			<tr><td>E-mail:</td><td><input type=\"text\" name=\"email\"></td></tr>
			<tr><td>{$lang['password']}:</td><td><input type=\"password\" name=\"password\"></td></tr>
			<tr><td colspan=\"2\"><input type=\"submit\" value=\"{$lang['save']}\"></td></tr>
			</table>
			</form>";
	} else {
		while($user=pg_fetch_all($db->result)) {
			$user_id=$user['user_id'];
		}
	}
}
function my_curl($link) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	return curl_exec($curl);
	curl_close($curl);
}
echo $close;
?>
