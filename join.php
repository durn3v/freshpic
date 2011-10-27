<?php
session_start();
include("config.php");

echo $start;
echo $after_scripts;

if(isset($_SESSION['user_id']))
{
header("Location: /{$_SESSION['user_id']}");
exit();
}
else
{
	if(isset($_GET['key']))
	{
		$dbconn = pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpass);
		$sql_key="SELECT key FROM preuser WHERE key='".$_GET['key']."'";
		if(pg_numrows(pg_query($sql_key))==0)
		{
			echo "Неправильная ссылка активации аккаунта";
			pg_close($dbconn);
		}
		else
		{
			$dbconn = pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpass);
			$sql_frompreuser = "SELECT * FROM preuser WHERE key='".$_GET['key']."'";
			$result_frompreuser = pg_query($sql_frompreuser) or die(pg_last_error());
			while ($info = pg_fetch_array($result_frompreuser))
			{
				$email=$info['email'];
				$pass=$info['pass'];
				$name=$info['name'];
				$lastname=$info['lastname'];
				$sex=$info['sex'];
				$date=$info['date'];
			}
			$sql_user = "INSERT INTO users
					(email,pass,name,lastname,sex,avatar,register_date)
					VALUES
					('".$email."','".$pass."','".$name."','".$lastname."','".$sex."','nothing','".$date."');
					DELETE FROM preuser WHERE key='".$_GET['key']."'";
			pg_query($sql_user) or die(pg_last_error());
			$sql_messages = "SELECT * FROM users WHERE email='".$email."'";
			$result_messages = pg_query($sql_messages) or die(pg_last_error());
			while ($users = pg_fetch_array($result_messages))
			{
				$user_id = $users['uid'];
			}
			pg_close($dbconn);
			mkdir("./i/{$user_id}");
			mkdir("./s/{$user_id}");
			mkdir("./p/{$user_id}");
			echo "$name вы успешно активировали свой аккаунт";
		}
	}
	elseif(isset($_POST['act']))
	{
		if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['ver_password']) && !empty($_POST['sex']))
		{
			if($_POST['password']==$_POST['ver_password'])
			{
				$dbconn = pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpass);
				$sql_mail="SELECT email FROM users WHERE email='".$_POST['email']."'";
				if(pg_numrows(pg_query($sql_mail))==0)
				{
					$sql_preuser = "INSERT INTO preuser 
					(email,pass,name,lastname,sex,key)
					VALUES
					('".$_POST['email']."','".md5($_POST['password'])."','".$_POST['firstname']."','".$_POST['lastname']."','".$_POST['sex']."','".md5($_POST['email'])."');";
					pg_query($sql_preuser);
					pg_close($dbconn);
					mail($_POST['email'], "Регистрация на freshpic.org", "Активируйте аккаунт по ссылке http://freshpic.org/join.php?key=".md5($_POST['email'])."",
					"From: noreply@freshpic.org\n"."Reply-To:"."X-Mailer: PHP/".phpversion());
					echo "Активируйте пожалуйста свой аккаунт";
				}
				else
				{
					echo "Пользователь с таким email адресом уже существует"; 
				}
			}
			else
			{
				echo "Пароли не совпадают";
			} 
		}
		else
		{
			echo "Не заполнены некоторые обязательные поля";
		}
	}
	else
	{
	echo "<form method=\"POST\" action=\"join.php\">";
        echo "<table style=\"text-align:right;\"><tr><td collspan=\"2\"><b>";
        echo $lang['registration'];
        echo "</b></td></tr><tr><td>";
	echo $lang['firstname'];
	echo ":</td><td><input type=\"text\" name=\"firstname\"></td></tr><tr><td>";
	echo $lang['lastname'].":</td><td><input type=\"text\" name=\"lastname\"></td></tr><tr><td>";
	echo $lang['email'].":</td><td><input type=\"text\" name=\"email\"></td></tr><tr><td>";
	echo $lang['password'].":</td><td><input type=\"password\" name=\"password\"></td></tr><tr><td>";
	echo $lang['ver_password'].":</td><td><input type=\"password\" name=\"ver_password\"></td></tr><tr><td>";
	echo $lang['sex'].": ".$lang['sex_m']."<input type=\"radio\" name=\"sex\" value=\"m\">";
	echo $lang['sex_f']."<input type=\"radio\" name=\"sex\" value=\"f\"></td></tr><tr><td>";

	echo "<input type=\"hidden\" name=\"act\">";
	echo "<input type=\"submit\" value=\"{$lang['registration']}\">
	</form></td></tr></table>";
	}
}
echo $close;

?> 
