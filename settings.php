<?php
session_start();
include("config.php");

if($_FILES['pic']['name']) {
	if(is_uploaded_file($_FILES["pic"]["tmp_name"]))
	{
		$db->connect();
		$db->action("SELECT images FROM counts WHERE user_id={$_SESSION['user_id']}");
		while($images=pg_fetch_array($db->result)) $uid=$images['images']+1;
		$db->action("UPDATE counts SET images='{$uid}' WHERE user_id={$_SESSION['user_id']}");
		$name = chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) ).chr( rand(97, 122) ).$uid.".jpg";
		$db->action("UPDATE users SET avatar='{$name}' WHERE uid={$_SESSION['user_id']}");
		$db->close();
		move_uploaded_file($_FILES["pic"]["tmp_name"], "./p/{$_SESSION['user_id']}/{$name}");
		imageresize("./s/{$_SESSION['user_id']}/{$name}","./p/{$_SESSION['user_id']}/{$name}",200,400,90, $_FILES["pic"]["type"]);
		list($width, $height, $type) = getimagesize("./s/{$_SESSION['user_id']}/{$name}");
	}
}

echo $start;
echo "<style>

.imgareaselect-border1 {
	background: url(border-v.gif) repeat-y left top;
}

.imgareaselect-border2 {
    background: url(border-h.gif) repeat-x left top;
}

.imgareaselect-border3 {
    background: url(border-v.gif) repeat-y right top;
}

.imgareaselect-border4 {
    background: url(border-h.gif) repeat-x left bottom;
}

.imgareaselect-border1, .imgareaselect-border2,
.imgareaselect-border3, .imgareaselect-border4 {
    filter: alpha(opacity=50);
	opacity: 0.5;
}

.imgareaselect-handle {
    background-color: #fff;
    border: solid 1px #000;
    filter: alpha(opacity=50);
    opacity: 0.5;
}

.imgareaselect-outer {
    background-color: #000;
    filter: alpha(opacity=50);
    opacity: 0.5;
}

.imgareaselect-selection {  
}</style>";
echo $after_title;
echo "<script type=\"text/javascript\" src=\"jquery.imgareaselect.pack.js\"></script>";
echo "<script type=\"text/javascript\">
function preview(img, selection) {

    var scaleX = 50 / selection.width;
    var scaleY = 50 / selection.height;

    $('#preview img').css({
        width: Math.round(scaleX * {$width}),
        height: Math.round(scaleY * {$height}),
        marginLeft: -Math.round(scaleX * selection.x1),
        marginTop: -Math.round(scaleY * selection.y1)
    });

    $('#x1').val(selection.x1);
    $('#y1').val(selection.y1);
    $('#x2').val(selection.x2);
    $('#y2').val(selection.y2);
    $('#w').val(selection.width);
    $('#h').val(selection.height);    
}

$(document).ready(function () {
    $('#photo').imgAreaSelect({ aspectRatio: '1:1', handles: true,
        fadeSpeed: 200, onInit: preview, onSelectChange: preview, x1: 0, y1: 0, x2: 100, y2: 100, minWidth: 50, minHeight: 50,});
});</script>";

echo $after_scripts;

function crop($file_input, $file_output, $crop = 'square',$percent = false) {
	list($w_i, $h_i, $type) = getimagesize($file_input);
	if (!$w_i || !$h_i) {
		echo 'Невозможно получить длину и ширину изображения';
		return;
    }
    $types = array('','gif','jpeg','png');
    $ext = $types[$type];
    if ($ext) {
    	$func = 'imagecreatefrom'.$ext;
    	$img = $func($file_input);
    } else {
    	echo 'Некорректный формат файла';
		return;
    }
	if ($crop == 'square') {
		$min = $w_i;
		if ($w_i > $h_i) $min = $h_i;
		$w_o = $h_o = $min;
	} else {
		list($x_o, $y_o, $w_o, $h_o) = $crop;
		if ($percent) {
			$w_o *= $w_i / 100;
			$h_o *= $h_i / 100;
			$x_o *= $w_i / 100;
			$y_o *= $h_i / 100;
		}
    	if ($w_o < 0) $w_o += $w_i;
	    $w_o -= $x_o;
	   	if ($h_o < 0) $h_o += $h_i;
		$h_o -= $y_o;
	}
	$img_o = imagecreatetruecolor($w_o, $h_o);
	imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
	if ($type == 2) {
		return imagejpeg($img_o,$file_output,100);
	} else {
		$func = 'image'.$ext;
		return $func($img_o,$file_output);
	}
}

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


if(isset($_POST['x1']))
{
$x1=$_POST['x1'];
$y1=$_POST['y1'];
$x2=$_POST['x2'];
$y2=$_POST['y2'];
crop("./s/{$_SESSION['user_id']}/{$_POST['name']}", "./i/{$_SESSION['user_id']}/{$_POST['name']}", array($x1,$y1,$x2,$y2));
imageresize("./i/{$_SESSION['user_id']}/{$_POST['name']}","./i/{$_SESSION['user_id']}/{$_POST['name']}",50,50,100, "image/jpeg");
echo "<img src=\"./s/{$_SESSION['user_id']}/{$_POST['name']}\"><br>";
echo "<img src=\"./i/{$_SESSION['user_id']}/{$_POST['name']}\">";
}

if($_FILES['pic']['name'])
{
	
		echo "<img id=\"photo\" src=\"./s/{$_SESSION['user_id']}/{$name}\">";
		echo "<div id=\"preview\" style=\"width:50px; height:50px; overflow: hidden;\">
		<img src=\"./s/{$_SESSION['user_id']}/{$name}\">
		</div>";
		echo "<form method=\"POST\">";
		echo "<input type=\"hidden\" id=\"x1\" name=\"x1\">";
		echo "<input type=\"hidden\" id=\"x2\" name=\"x2\">";
		echo "<input type=\"hidden\" id=\"y1\" name=\"y1\">";
		echo "<input type=\"hidden\" id=\"y2\" name=\"y2\">";
		echo "<input type=\"hidden\" name=\"name\" value=\"{$name}\">";
		echo "<br><input type=\"submit\" value=\"{$lang['save']}\">";
		echo "</form>";
	
}

	echo 	"<form enctype=\"multipart/form-data\" method=\"POST\">
		{$lang['upload_profile_photo']}<br><input type=\"file\" name=\"pic\">
		<input type=\"submit\" value=\"{$lang['submit']}\">
		</form>";

echo $close;

?>
