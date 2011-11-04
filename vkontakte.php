<?php
session_start();
include_once("config.php");
echo $start;
echo $after_title;
echo $after_scripts;
if(isset($_SESSION['vk_access_token']) and isset($_SESSION['vk_user_id']))
{
	$db->connect();
	$db->action("SELECT * FROM vk_users WHERE vk_user_id={$_SESSION['vk_user_id']}");
	if(pg_num_rows($db->result)==0) {
		$for_curl="https://api.vkontakte.ru/method/getProfiles?uid=".$_SESSION['vk_user_id']."&fields=city,country,photo_rec,photo_big&access_token=".$_SESSION['vk_access_token'];
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
		
		echo "<table><tr><td><img src=\"{$user['photo_rec']}\"></td><td>{$user['first_name']} {$user['last_name']}</td></tr><tr><td colspan=\"2\">{$country['name']},{$city['name']}</td></tr></table>";
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
