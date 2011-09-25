<?php

//define $lang with value from cookie or, if cookie does not contain it yet, over geoip	
if(isset($_COOKIE['language']))
{
	$language = $_COOKIE['language'];
}
else
{
	$code = strtolower(geoip_country_code_by_name($_SERVER['REMOTE_ADDR']));
	if($code == "ru")
	{
		$language = "ru";
	}
	elseif($code == "de")
	{
		$language = "de";
	}
	else
	{
		$language = "en";
	}
	setcookie("language", $language, time()+3600*24*30, "/");
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

include("languages/".$language.".php");

$hostname = "freshpic.org";
$dbhost = "localhost";
$dbname = "freshpic";
$dbuser = "freshpic";
$dbpass = "ui6Uph6X";

class db {

function connect() {
	$hostname = "freshpic.org";
	$dbhost = "localhost";
	$dbname = "freshpic";
	$dbuser = "freshpic";
	$dbpass = "ui6Uph6X";
	$this->connect=pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpass);
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

$start="<html><head><meta http-equiv=\"Content-type\" content=\"test/html; charset=utf-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" /><script src=\"jquery.js\"></script>
	<script>
	var messages = $(\"#messages\").html();
	function new_messages()  
	  {  
	      $.ajax({  
		  url: \"actions/messages/new_messages.php\",  
		  cache: false,  
		  success: function(data){  
		      if(data==0) {
			if(messages==data){} else {
			$(\"#messages\").html(\"\");
			$('title').html('{$lang['messages']}');
			}
			}
		      else {
		      if(data==1){
			if(messages==data){} else {
			$(\"#messages\").html(data);
			$('title').html('{$lang['new_message']}');}}
		      if(data>1) {
			if(messages==data){} else {
			$(\"#messages\").html(data);
			$('title').html('{$lang['new_messages']}');}
			}
			
		      }
		}  
		});  
	}
	$(document).ready(function(){
		new_messages();  
		setInterval('new_messages()',1000);  
		});
	</script>";

if(isset($_SESSION['user_id']))
{
	$menu="<a class=\"top_link\" href=\"home.php\">{$lang['profile']}</a>
	<a class=\"top_link\" href=\"mail.php\">{$lang['messages']}<span id=\"messages\">{$new_messages}</span></a>
	<a class=\"top_link\" href=\"home.php?act=logout\">{$lang['log_out']}</a>";
} else {
	$menu="FreshPic.org";
}
$after_scripts="</head>
	<body><div class=\"top\">
	{$menu}
	</div><div class=\"main\">";

$close= "<div class=\"lang\">
	<a href=\"?lang=ru\">Русский</a>
	<a href=\"?lang=en\">English</a>
	<a href=\"?lang=de\">Deutsch</a></div>
	</div></body></html>";

?>