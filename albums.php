<?php
session_start();
include("config.php");

if(!isset($_GET['act']) and !isset($_GET['user']) and !isset($_FILES['pic']) and isset($_SESSION['user_id']))
{
Header("Location: albums.php?user={$_SESSION['user_id']}");
}

echo $start;
echo "<title>{$lang['albums']}</title>";
echo $after_title;
echo "<script>
var js_title='{$lang['albums']}';
      $(document).ready(function(){
      var i = $('input').size() + 1;
	
	$('#add').click(function() {
		$('<input type=\"file\" name=\"pic[]\"><br>').fadeIn('fast').appendTo('.inputs');
		i++;
	});});</script>";
echo $after_scripts;

function imageresize($outfile,$infile,$neww,$newh,$quality,$type)
	{
	if($type == "image/jpeg")
	{
	$im=imagecreatefromjpeg($infile);
	} 
	elseif($type == "image/png" or $type == "image/x-png")
	{
	$im=imagecreatefrompng($infile);
	} 
	elseif($type == "image/gif")
	{
	$im=imagecreatefromgif($infile);
	}
	elseif($type == "image/bmp")
	{
	$im=imagecreatefrombmp($infile);
	}
	$k1=$neww/imagesx($im);
	$k2=$newh/imagesy($im);
	$k=$k1>$k2?$k2:$k1;

	$w=intval(imagesx($im)*$k);
	$h=intval(imagesy($im)*$k);

	$im1=imagecreatetruecolor($w,$h);
	if($type == "image/png" or $type == "image/x-png")
	{
	imagealphablending($im1, false);
	imagesavealpha($im1, true);
	}
	imagecopyresampled($im1,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

	if($type == "image/png" or $type == "image/x-png")
	{
	imagepng($im1,$outfile);
	}
	else
	{
	imagejpeg($im1,$outfile,$quality);
	}
	imagedestroy($im);
	imagedestroy($im1);
}

if(isset($_SESSION['user_id']))
{

if(isset($_GET['user']))
{
	if(!isset($_GET['album']))
	{
		$db->connect();
		$db->action("SELECT * FROM albums WHERE user_id={$_GET['user']}");
		echo "<table>";
		if(pg_num_rows($db->result)==0)
		{
			echo "{$lang['no_albums']}";
		} else {
			while($album=pg_fetch_array($db->result))
			{
			$name=$album['name'];
			$album_id=$album['album_id'];
			$count=$album['count'];
			$cover=$album['cover'];
			echo "<tr><td><a href=\"?user={$_GET['user']}&album={$album_id}\"><img src=\"./i/{$cover}\"></a></td><td><a href=\"?user={$_GET['user']}&album={$album_id}\">{$name}</a></td></tr>";
			}
		}
		echo "</table>";
		$db->close();
	} else {
		$db->connect();
		$db->action("SELECT name FROM images WHERE user_id={$_GET['user']} AND album_id={$_GET['album']}");
		$i=0;
		while($images=pg_fetch_array($db->result))
		{
		if($i % 4 === 0) echo "<br>";
			echo "<img src=\"./i/{$images['name']}\">";
			$i++;
		}
		$db->close();
	}
} else {
	if($_FILES['pic'])
	{
		$db->connect();
		$db->action("SELECT count FROM albums WHERE user_id={$_SESSION['user_id']}");
		while($albums=pg_fetch_array($db->result)) $count=$albums['count'];
		$i=1;
		foreach($_FILES['pic']['tmp_name'] as $file) 
		{
			if(is_uploaded_file($file))
			{
				$db->action("SELECT images FROM counts WHERE user_id={$_SESSION['user_id']}");
				while($images=pg_fetch_array($db->result)) $uid=$images['images']+1;
				$db->action("UPDATE counts SET images='{$uid}' WHERE user_id={$_SESSION['user_id']}");
				$name = chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) ).		$uid.".jpg";
				if($i==1) $db->action("UPDATE albums SET cover='{$name}' WHERE user_id={$_SESSION['user_id']} AND album_id={$_POST['album_id']}");
				$db->action("INSERT INTO images (user_id,album_id,name,seq) VALUES ({$_SESSION['user_id']}, {$_POST['album_id']}, '{$name}', {$i});");
				move_uploaded_file($file, "./p/{$name}");
				imageresize("./i/{$name}","./p/{$name}",100,100,90, "image/jpeg");
				imageresize("./s/{$name}","./p/{$name}",800,600,90, "image/jpeg");
				//list($width, $height, $type) = getimagesize("./s/{$name}");
				echo "<img src=\"./i/{$name}\">";
				$count++;
				$i++;
			}
		}
		$db->action("UPDATE albums SET count={$count} WHERE user_id={$_SESSION['user_id']} AND album_id={$_POST['album_id']}");
		$db->close();
		
	} 
	if(isset($_POST['name']) and isset($_POST['description']))
	{
		$db->connect();
		$db->action("SELECT albums FROM counts WHERE user_id={$_SESSION['user_id']}");
		while($counts=pg_fetch_array($db->result))
		{
			$album_id=$counts['albums']+1;
		}
		$db->action("INSERT INTO albums (user_id,name,description,album_id) VALUES ({$_SESSION['user_id']},'{$_POST['name']}','{$_POST['description']}',{$album_id});");
		$db->action("UPDATE counts SET albums={$album_id}");
		$db->close();
		echo "{$_GET['name']}<br>";
		echo "{$lang['upload_some_photos']}:<br>";
		echo "<form  method=\"POST\" enctype=\"multipart/form-data\" action=\"albums.php\">";
		echo "<div class=\"inputs\">";
		echo "<input type=\"file\" name=\"pic[]\" multiple=\"true\"><br>";
		echo "</div>";
		echo "<a href=\"#\" id=\"add\">add input</a><br>";
		echo "<input type=\"submit\" value=\"{$lang['submit']}\">";
		echo "<input type=\"hidden\" value=\"{$album_id}\" name=\"album_id\">";
		echo "</form>";
	}

	if($_GET['act']=="new" and !isset($_POST['name']))
	{
		echo "<form method=\"POST\" action=\"albums.php?act=new\">";
		echo "{$lang['name_album']}: <input type=\"text\" name=\"name\"><br>";
		echo "{$lang['description']}: <input type=\"text\" name=\"description\"><br>";
		echo "<input type=\"submit\" value=\"{$lang['save']}\">";
		echo "</form>";
	}
}
if($_GET['user']==$_SESSION['user_id'])
{
	echo "<a href=\"?act=new\">{$lang['new_album']}</a><br>";
}
}

echo $close;
?>
