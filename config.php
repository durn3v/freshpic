<?php

if(isset($_COOKIE['remember']))
{
	if(!isset($_SESSION['user_id']))
	{
		$_SESSION['user_id']=$_COOKIE['remember'];
	}
}
//define $lang with value from cookie or, if cookie does not contain it yet, over geoip	
if(isset($_COOKIE['language']))
{
	define("USER_LANGUAGE",$_COOKIE['language']);
}
else
{
	$code = strtolower(geoip_country_code_by_name($_SERVER['REMOTE_ADDR']));
	if($code == "ru")
	{
		define("USER_LANGUAGE","ru");
	}
	elseif($code == "de")
	{
		define("USER_LANGUAGE","de");
	}
	else
	{
		define("USER_LANGUAGE","en");
	}
	setcookie("language", USER_LANGUAGE, time()+3600*24*30, "/");
}

//if pressed flag image
if($_GET['lang']=="en")
{
	setcookie("language", "en", time()+3600*24*30, "/");
	header("Location: {$_SERVER['HTTP_REFERER']}");
}
elseif($_GET['lang']=="ru")
{
	setcookie("language", "ru", time()+3600*24*30, "/");
	header("Location: {$_SERVER['HTTP_REFERER']}");
}
elseif($_GET['lang']=="de")
{
	setcookie("language", "de", time()+3600*24*30, "/");
	header("Location: {$_SERVER['HTTP_REFERER']}");
}

require_once("languages/".USER_LANGUAGE.".php");

define("HOST_NAME","freshpic.org");
define("DB_HOST","localhost");
define("DB_NAME","freshpic");
define("DB_USER","freshpic");
define("DB_PASS","GaopI4");

class db {

function connect() {
	$this->connect=pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS);
	}
function action($sql) {
	$this->sql=$sql;
	$this->result=pg_query($this->sql) or die(pg_last_error());
	}
function free() {
	pg_free_result($this->result);
	}
function close() {
	pg_close($this->connect);
	}
}

$db=new db;

function db_connect() {
	return pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS);
}
function db_close($connect) {
	pg_close($connect);
}

if(isset($_SESSION['user_id'])){
$db->connect();
$db->action("SELECT new_messages FROM counts WHERE user_id=".$_SESSION['user_id']);
	while ($result = pg_fetch_array($db->result)) {
	$new=$result['new_messages'];	
	}
		if($new==0) {
		$new_messages = "";
		} else {
		$new_messages = $new;
	}
$db->close();
}

$start="<html><head><meta http-equiv=\"Content-type\" content=\"test/html; charset=utf-8\">";
$after_title="<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" /><script src=\"jquery.js\"></script>";

if(isset($_SESSION['user_id']))
{
	$menu="<div class=\"top\">
	<a class=\"top_link\" href=\"{$_SESSION['user_id']}\">{$lang['profile']}</a>
	<a class=\"top_link\" href=\"albums.php\">{$lang['albums']}</a>
	<a class=\"top_link\" href=\"mail.php\">{$lang['messages']}<span id=\"messages\">{$new_messages}</span></a>
	<a class=\"top_link\" href=\"feed.php\">{$lang['feed']}</a>
	<a class=\"top_link\" href=\"settings.php\">{$lang['settings']}</a>
	<a class=\"log_out_link\" href=\"home.php?act=logout\">{$lang['log_out']}</a>
	</div>";
} else {
	$menu="<div class=\"top\"></div>";
}
$after_scripts="<script>
	var messages = $(\"#messages\").html();
	var title = $('title').html();
	function new_messages()  
	  {  
	      $.ajax({  
		  url: \"actions/messages/new_messages.php\",  
		  cache: false,  
		  success: function(data){  
		      if(data==0) {
			if(messages==data){} else {
			$(\"#messages\").html(\"\");
			$('title').html(title);
			}
			}
		      else {
		      if(data>0){
			if(messages==data){} else {
			$(\"#messages\").html(data);
			$('title').html(title+' (!)');}}
		      }
		}  
		});  
	}
	function online()
	{
		$.ajax({
			url: \"actions/user/online.php\",
			cache: false,
			success: function(){}
		});
	}
	$(document).ready(function(){
		new_messages();
		online();
		setInterval('new_messages()',1000);  
		setInterval('online()',30000);
		});
</script></head>
	<body>
	{$menu}
	<div class=\"main\">";



$close= "</div><div class=\"lang\">
	<a href=\"&lang=ru\">Русский</a>
	<a href=\"&lang=en\">English</a>
	<a href=\"&lang=de\">Deutsch</a>
	</div></body></html>";

?>
