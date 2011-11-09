<?php
session_start();
include_once("config.php");
if(isset($_POST['email']) && isset($_POST['pass']))
{
	$email=$_POST['email'];
	$pass=md5($_POST['pass']);
	$db->connect();
	$db->action("SELECT * FROM users WHERE email='".$email."'");
	while ($sign = pg_fetch_array($db->result))
	{
		$passtwo=$sign['pass'];
		$user_id=$sign['uid'];
	}
	if($pass==$passtwo)
	{
		$_SESSION['user_id']=$user_id;
		if($_POST['remember']=="yes")
		{
			setcookie("user_id", $user_id, time()+3600*24*30, "/");
			setcookie("user_password", $pass, time()+3600*24*30, "/");
		}
	}
	header("Location: home.php");
	exit();
}

if(isset($_GET['code']))
{
	$vk_code=$_GET['code'];
	$out = my_curl('https://api.vkontakte.ru/oauth/access_token?client_id=2661341&client_secret=THISIS&code='.$vk_code);
	$json_data=(array)json_decode($out);
	$db->connect();
	$db->action("SELECT * FROM vk_users WHERE vk_user_id={$json_data['user_id']}");
	if(pg_num_rows($db->result)!=0)
	{
		while($result=pg_fetch_array($db->result))
		{
			$_SESSION['user_id']=$result['user_id'];
			header("Location: /{$result['user_id']}");
			exit();
		}
	} else {
		$_SESSION['vk_access_token']=$json_data['access_token'];
		$_SESSION['vk_user_id']=$json_data['user_id'];
		header("Location: vkontakte.php");
	}
	$db->close();
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

if(isset($_GET['act'])=="logout")
{
	setcookie("user_id","");
	setcookie("user_password","");
	unset($_SESSION['user_id']);
	header("Location: ./");
}

?>
