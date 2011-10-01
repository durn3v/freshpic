<?php
session_start();
include("config.php");

function from_user($from_id) {
$sql_from="SELECT * FROM users WHERE uid=".$from_id;
$result_user=pg_query($sql_from) or die(pg_last_error());
while ($from_user = pg_fetch_array($result_user)) { return $from_user['name']." ".$from_user['lastname']; }
}
function avatar($uid) {
$sql="SELECT avatar FROM users WHERE uid={$uid}";
$result=pg_query($sql) or die(pg_last_error());
while($users=pg_fetch_array($result)) 
	{return $users['avatar'];}
}

echo $start;
echo "<title>{$lang['messages']}</title>";
echo $after_title;
echo "<script>
var js_title='{$lang['messages']}';
        $(document).ready(function(){  
          
            $('#send').submit(function(){  
                $.ajax({  
                    type: \"POST\",  
                    url: \"/actions/messages/send_message.php\",  
                    data: \"&to=\"+$(\"#to\").val()+\"&subject=\"+$(\"#subject\").val()+\"&message=\"+$(\"#message\").val(),  
                    success: function(html){  
                        $(\"#content\").html(html);
                        document.location.href = \"mail.php\";
                    }  
                });  
                return false;  
            });  
 
        });  
</script>";
echo $after_scripts;

if(isset($_SESSION['user_id']))
{

	switch($_GET['act']):

	case "write":
		if(isset($_GET['to'])) {
			$value="value=\"{$_GET['to']}\"";
		} else 
		{ $value=""; }
		
		echo "<form id=\"send\">
		To: <input type=\"text\" id=\"to\" {$value}><br>
		Subject: <input type=\"text\" id=\"subject\"><br>
		Message: <textarea id=\"message\"></textarea><br>
		<input type=\"submit\" value=\"Send\">
		</form>";
		echo "<div id=\"content\"></div>";
		break;

	case "show":
		$db->connect();
		if(isset($_GET['out'])) {
		
		$db->action("SELECT * FROM messages WHERE from_id=".$_SESSION['user_id']." AND message_id_from=".$_GET['id']);
		
		while($message = pg_fetch_array($db->result)){
			$to_id=$message['to_id'];
			$user=from_user($to_id);
			echo "to: ".$user."<br>Subject: ".$message['subject']."<br>Message: ".$message['message']."<br>";
			echo 	"Reply:<form id=\"send\">
				<input type=\"hidden\" id=\"to\" value=\"".$to_id."\"><br>
				Subject: <input type=\"text\" id=\"subject\"><br>
				Message: <textarea id=\"message\"></textarea><br>
				<input type=\"submit\" value=\"Send\">
				</form>";
			$uid=$message['uid'];
			}
			
		$db->close();
		
		} else {
		$db->action("SELECT * FROM messages WHERE to_id=".$_SESSION['user_id']." AND message_id_to=".$_GET['id']);
		
		while($message = pg_fetch_array($db->result)){
			$from_id=$message['from_id'];
			$user=from_user($from_id);
			echo "from: ".$user."<br>Subject: ".$message['subject']."<br>Message: ".$message['message']."<br>";
			echo 	"Reply:<form id=\"send\">
				<input type=\"hidden\" id=\"to\" value=\"".$message['from_id']."\"><br>
				Subject: <input type=\"text\" id=\"subject\"><br>
				Message: <textarea id=\"message\"></textarea><br>
				<input type=\"submit\" value=\"Send\">
				</form>";
			$read_status=$message['read_status'];
			$uid=$message['uid'];
			}
			
		if($read_status=="u") {
				$db->action("UPDATE messages SET read_status='r' WHERE uid=".$uid);
				$db->action("UPDATE counts SET new_messages=new_messages-1 WHERE user_id=".$_SESSION['user_id']);
			}
		}

		$db->close();
		break;

	case "outbox":
		$db->connect();
		$db->action("SELECT * FROM messages WHERE from_id='".$_SESSION['user_id']."' ORDER BY uid DESC");
		while ($messages = pg_fetch_array($db->result)) {
		      $user=from_user($messages['to_id']);
		      $avatar=avatar($messages['to_id']);
		      echo "<a href=\"?act=show&id=".$messages['message_id_from']."&out\">";
		      echo "<div class=\"message\">";
		      echo "<table><tr><td width=\"50\">";
		      if($avatar!="nothing") echo "<img src=\"./i/{$avatar}\">";
		      echo "</td><td>{$user}<br>{$messages['message']}</td></tr></table>";
		      echo "</div></a>";
		}
		echo "<a href=\"?\">{$lang['inbox']}</a><br>";
		$db->close();
		break;

	default:
		$db->connect();
		if(isset($_GET['delete']))
		{
		$db->action("DELETE FROM messages WHERE to_id=".$_SESSION['user_id']." AND message_id_to=".$_GET['delete']);
		header("Location: mail.php");
		}
		$db->action("SELECT * FROM messages WHERE to_id='".$_SESSION['user_id']."' ORDER BY uid DESC");
		while ($messages = pg_fetch_array($db->result)) {
			if($messages['read_status']=="u") {$message_style="unread_message";} else {$message_style="message";}
			$user=from_user($messages['from_id']);
			$message_id=$messages['message_id_to'];
			echo "<a href=\"?act=show&id=".$message_id."\" class=\"message_link\">";
			echo "<div class=\"{$message_style}\">";
			echo "<table><tr><td width=\"50\">";
			$avatar=avatar($messages['from_id']);
			if($avatar!="nothing") echo "<img src=\"./i/{$avatar}\">";
			echo "</td><td>{$user}<br>{$messages['message']}</td></tr></table>";
			//echo "<a href=\"?delete={$message_id};\">{$lang['delete']}</a>";
			echo "</div>";
			echo "</a>";
		}
		echo "<a href=\"?act=outbox\">{$lang['outbox']}</a><br>";
		$db->close();
	endswitch;
	echo "<a href=\"?act=write\">{$lang['write_a_message']}</a>";

}
else
{
	header("Location: home.php");
}

echo $close;

?>
