<?php
session_start();
include_once("config.php");

function user($from_id) {
$sql_from="SELECT * FROM users WHERE uid=".$from_id;
$result_user=pg_query($sql_from) or die(pg_last_error());
while ($from_user = pg_fetch_array($result_user)) { return $from_user['name']." ".$from_user['lastname']; }
}
if(isset($_SESSION['user_id']))
{
	if(!isset($_GET['user'])) { header("Location: /".$_SESSION['user_id']);}

	if(isset($_GET['user']) && isset($_GET['follow'])){
		$db->connect();
		$db->action("SELECT * FROM followers WHERE who='".$_SESSION['user_id']."' AND whom='".$_GET['user']."'");
		if(pg_num_rows($db->result)==0){
		$db->action("INSERT INTO followers (who,whom) VALUES (".$_SESSION['user_id'].",".$_GET['user'].")");
		$db->action("UPDATE counts SET following=following+1 WHERE user_id=".$_SESSION['user_id']);
		$db->action("UPDATE counts SET followers=followers+1 WHERE user_id=".$_GET['user']);
		}
		$db->close();
		header("Location: /{$_GET['user']}");
		exit();
	}
	if(isset($_GET['user']) && isset($_GET['unfollow'])){
		$db->connect();
		$db->action("DELETE FROM followers WHERE who='".$_SESSION['user_id']."' AND whom='".$_GET['user']."'");
		$db->action("UPDATE counts SET following=following-1 WHERE user_id=".$_SESSION['user_id']);
		$db->action("UPDATE counts SET followers=followers-1 WHERE user_id=".$_GET['user']);	
		$db->close();
		header("Location: /{$_GET['user']}");
		exit();
	}
}
echo $start;
echo "<title>Home</title>";
echo $after_scripts;
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
		header("Location: home.php");
		exit();
	}
}

if(isset($_GET['act'])=="logout")
{
	unset($_SESSION['user_id']);
	header("Location: ./");
}

if(isset($_SESSION['user_id']))
{
	if(isset($_GET['user']))
	{
		$db->connect();
		$db->action("SELECT * FROM users WHERE uid='".$_GET['user']."'");
		if(pg_num_rows($db->result)==0) {
			header("Loaction: home.php");
		} else {
			while ($user = pg_fetch_array($db->result))
			{
			$name=$user['name'];
			$lastname=$user['lastname'];
			}
		}
		$user=pg_fetch_array($db->result);
		if($user['online_time']+30<time()) echo "online<br>";
		echo $name." ".$lastname;
		}
		if($_GET['user']==$_SESSION['user_id'])
		{ 
			echo " ({$lang['that_is_you']})";	
		} else
		{ 
			echo "<br><a href=\"mail.php?act=write&to={$_GET['user']}\">{$lang['write_a_message']}</a>";
			$db->action("SELECT * FROM followers WHERE who='".$_SESSION['user_id']."' AND whom='".$_GET['user']."'");
			echo "<br>";
			if(pg_num_rows($db->result)==0) {
				echo "<a href=\"{$_GET['user']}&follow\">{$lang['follow']}</a>";
				} else {
				echo $lang['following']." | <a href=\"{$_GET['user']}&unfollow\">{$lang['unfollow']}</a>";	
				}
		}
		$db->action("SELECT following FROM counts WHERE user_id=".$_GET['user']);
		$following=pg_fetch_array($db->result);
		if($following['following']>0) {
			echo "<br>".$lang['following_this_people'];
			echo " ".$following['following']. " :";
			$db->action("SELECT * FROM followers WHERE who='".$_GET['user']."'");
			while($following_user=pg_fetch_array($db->result)) {
			$user=user($following_user['whom']);
			echo "<a href=\"{$following_user['whom']}\">$user</a>\n";
			}
		}
		
		$db->action("SELECT followers FROM counts WHERE user_id=".$_GET['user']);
		$followers=pg_fetch_array($db->result);
		if($followers['followers']>0) {
			echo "<br>".$lang['followers'];
			echo " ".$followers['followers']. " :";
			$db->action("SELECT * FROM followers WHERE whom='".$_GET['user']."'");
			while($follower=pg_fetch_array($db->result)) {
			$user=user($follower['who']);
			echo "<a href=\"{$follower['who']}\">$user</a>\n";
			}
	}
}
else
{
	echo "<form method=\"POST\">
		{$lang['email']}: <input type=\"text\" name=\"email\">
		{$lang['password']}<input type=\"password\" name=\"pass\">
		<input type=\"submit\" value=\"{$lang['sign_in']}\">
		</form>";
}
$db->close();
echo $close;

?>
