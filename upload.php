<?php

include("config.php");

$uploadfile = $uploaddir . basename($_FILES['pic']['name']);
$dir=dirname($_SERVER['SCRIPT_NAME']);
$server=$_SERVER['SERVER_NAME'];
$path = "http://$server$dir";
$description=strip_tags($_POST['description']);
$description=substr($description,0,100);

		//upload block

if($_FILES["pic"]["name"])
{
	$size = '0';
  	if($_FILES["pic"]["size"] > 1024*50*1024)
	{	
		echo "size error";
		$size = '1';
	}
	if($size == '0')
	{
		if(is_uploaded_file($_FILES["pic"]["tmp_name"]))
		{	
			$type = $_FILES["pic"]["type"];
			if($type == "image/jpeg" or $type == "image/png" or $type == "image/gif" or $type == "image/vnd.microsoft.icon" or $type == "image/bmp" or $type == "image/x-icon" or $type == "image/x-png")
			{
				$dbconn = pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpass);
				$sql = @"SELECT uid FROM images 
						ORDER BY uid DESC LIMIT 1	
				";
				$result = pg_query($sql) or die('Query failed: ' . pg_last_error());

				while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
				{
					foreach ($line as $col_value)
					{
						$uid = $col_value;
					}
				}
				$uid = $uid + 1;
				$add_1 = chr( rand(65, 90) );
				$add_2 = chr( rand(97, 122) );

				$str = $_FILES["pic"]["name"];
				$expl = explode(".",$str);
				$ext = end($expl);

				$name = $uid . $add_1 . $add_2;

				move_uploaded_file($_FILES["pic"]["tmp_name"], "./p/".$name.".".$ext);
				list($width, $height) = getimagesize("./p/".$name.".".$ext);
				if($width<200 or $height<200)
				{
					$little=1;
				}
				else
				{
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

					imageresize("./i/".$name.".".$ext,"./p/".$name.".".$ext,120,120,75, $_FILES["pic"]["type"]);

				}
								
				$date = date("Y-m-d H:i");
				$sql = @"INSERT INTO images
						(name,ext,options,date,ip,uniq_visitors)
						VALUES
						(
						'".$name."','".$ext."','".$description."','".$date."','".$_SERVER['REMOTE_ADDR']."',1
						)
				";
				pg_query($sql) or die('Query failed: ' . pg_last_error());
				$sql = @"INSERT INTO uniq_visitors
						(uid_images,ip)
						VALUES
						(
						".$uid.",'".$_SERVER['REMOTE_ADDR']."'
						)
				";
				pg_query($sql) or die('Query failed: ' . pg_last_error());
				pg_free_result($result);
				pg_close($dbconn);
						
				if(isset($_COOKIE['links']))
				{
					setcookie("links", $name . " " . $_COOKIE['links'], time()+3600*24*15, "/");
				}
				else
				{
					setcookie("links", $name, time()+3600*24*15, "/");
				}
				echo @'<div class="sure">Are you sure?<br><input type="button" value="Yes" onclick="$(\'div.sure\').fadeOut(\'fast\'); $(\'div.vis\').fadeOut(\'fast\'); $(\'div.pic\').fadeOut(\'fast\');"><input type="button" value="no" onclick="$(\'div.sure\').fadeOut(\'fast\');"></div>

				<div class="pic">
				<div style="right:2px; position:absolute;" onclick="$(\'div.vis\').fadeOut(\'fast\'); $(\'div.pic\').fadeOut(\'fast\');"><a href="">Close</a></div>
				<a href="p/'; echo $name.".".$ext; echo '" target="_blank" title="open in full size">
				<img style="margin:20px; max-width:80%; max-height:80%;" align="center" src="p/'; echo $name.".".$ext; echo '">
				</a><br>';
				if($_POST['description'] != '')
				{
					$string = desc_image_page_.$lang;
					echo "<br>".$$string.": ".$_POST['description'];
				}

				echo '<a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-lang="'; echo $lang.'"></a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				</div>';
				echo '<script language="javascript">update_links()</script>';
				echo "<script language=\"javascript\">$('div.vis').fadeIn('fast'); $('div.pic').fadeIn('fast');</script>";
				//echo '<noscript>you should enable javascript';
				//header("Refresh: 0; url=/".$name);
				//echo '</noscript>';
				//echo "<center>";
				//echo '<div id="link">';
				//echo '<span class="span_link">';
				//$string = link_.$lang;
				//echo "<span style='float:left'>".$$string.":</span>	http://".$hostname."/".$name;
				//echo "</span>";
				//echo "</div>";
				//echo "</center>";
				$upres="1";
						
			}
			else
			{
				$upres = 2;
			}
		}
		else
		{
			echo "error upload";
		}
	}
}
			
?>

<script language="javascript" type="text/javascript">
window.top.window.stop(<?php echo "$upres"; ?>,'<?php echo $name.".".$ext; ?>','<? echo "img".$name; ?>', '<? echo $description; ?>', '<? echo $little; ?>');
</script>
