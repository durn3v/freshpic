<?php
session_start();

if(isset($_SESSION['user_id']))
{
	header("Location: home.php");
}

include_once("config.php");

echo $start;

if(strpos($_SERVER['HTTP_USER_AGENT'],"Chrome"))
{
	$inputfocus = "onclick=\"this.select()\"";
}
else
{
	$inputfocus = "onfocus=\"this.select()\" onclick=\"this.select()\"";
}

?>

<script language="javascript" type="text/javascript">
function start() {
	document.getElementById('process').style.display = 'inline';
 	document.getElementById('upform').style.display = 'none';
    return true;
	}

function stop(result, link, url, description, little) {
	var success = '';
      if (result == 1){
	  if (little == 1)
	  {
	  if (description == ''){
	document.getElementById('pic').innerHTML ='<a href="http://freshpic.org/'+url+'" target="_blank"><img src="http://freshpic.org/p/'+link+'"></a><br><input type="text" style="margin:5px;" size="30" <? echo $inputfocus; ?> value="http://freshpic.org/'+url+'" readonly/>';
	} else {
	document.getElementById('pic').innerHTML ='<a href="http://freshpic.org/'+url+'" target="_blank"><img src="http://freshpic.org/p/'+link+'"></a><br><input type="text" style="margin:5px;" size="30" <? echo $inputfocus; ?> value="http://freshpic.org/'+url+'" readonly/><br>'+description;
	}
	} else {
	if (description == ''){
	document.getElementById('pic').innerHTML ='<a href="http://freshpic.org/'+url+'" target="_blank"><img src="http://freshpic.org/i/'+link+'"></a><br><input type="text" style="margin:5px;" size="30" <? echo $inputfocus; ?> value="http://freshpic.org/'+url+'" readonly/>';
	} else {
	document.getElementById('pic').innerHTML ='<a href="http://freshpic.org/'+url+'" target="_blank"><img src="http://freshpic.org/i/'+link+'"></a><br><input type="text" style="margin:5px;" size="30" <? echo $inputfocus; ?> value="http://freshpic.org/'+url+'" readonly/><br>'+description;
	}
	}
	$('#main').css('display','none');	
 }
      if (result == 2) {
      document.getElementById('main').innerHTML = "<div id='unsupported_type_true'><span class='span_unsupported'><p style='margin-top: 3px;'><? $string = unsupported_.$lang; echo $$string . "</p>"; ?></span></div>";W
	  }
      document.getElementById('process').style.display = 'none';
      document.getElementById('upform').style.display = '';

      return true;
	}
	</script>
	
<?php 

$image_request = '0';

echo $after_title;
echo $after_scripts;

echo @'<div id="index">
		<div id="upload_box">
		<div class="top">';


echo "	<form method=\"POST\" action=\"home.php\">
	E-mail: <input type=\"text\" name=\"email\">";
	echo $lang['password'].": <input type=\"password\" name=\"pass\">
	<input type=\"submit\" value=\"sign in\">
        </form>";
        
echo "</div>";

//check if image requested
if(isset($_GET['p']))
{
	//check if image exists
	$db->connect();
	$db->action("SELECT ext FROM images 
				WHERE name = '".$_GET['p']."'
	");
	while ($line = pg_fetch_array($db->result, null, PGSQL_ASSOC))
	{
		foreach ($line as $col_value)
		{
			$ext = $col_value;
		}
	}	
					
	//ext is empty = there is no a record of image in database = image does not exist
	if($ext != "")
	{
		$img=$_GET['p'].".".$ext;
		echo @'
		<a href="/p/'.$img.'" target="_blank" title="open in full size">
		<img style="margin:20px; max-width:80%; max-height:80%;" align="center" src="p/'; echo $_GET['p'].".".$ext; echo '">
		</a><br>
		<input type="text" size="30"'; echo $inputfocus; echo' value="http://freshpic.org/img'; echo $_GET['p']; echo '" readonly/>
		<br>';
						
		//echo '<div align="center" id="imagepage">';
		//echo "<a href='p/".$_GET['p'].".".$ext."'><img src='p/".$_GET['p'].".".$ext."' class='imagepage'></a>";
		$db->action("SELECT uid,visits,uniq_visitors,options FROM images
				WHERE name = '" . $_GET['p'] . "'
		");
		$row = pg_fetch_row($db->result);
		$visits = $row[1];
		$uid = $row[0];
		$uniq_visitors = $row[2];
		$options = $row[3];
		$visits = $visits + 1;
		$db->action("UPDATE images
				SET visits = ".$visits."
				WHERE name = '".$_GET['p']."'
		");
		$ip = $_SERVER['REMOTE_ADDR'];
		$db->action("SELECT ip FROM uniq_visitors
				WHERE uid_images = ".$uid."
		");
		$check = '0';
		while ($line = pg_fetch_array($db->result, null, PGSQL_ASSOC))
		{
			foreach ($line as $col_value)
			{
				if($ip == $col_value)
				{
					$check = '1';
				}
			}
		}
		if($check == '0')	//client's ip is not found in `uniq_visitors` table
		{
			$uniq_visitors = $uniq_visitors + 1;
			$db->action(@"INSERT INTO uniq_visitors
					(uid_images,ip)
					VALUES
					(
					".$uid.",'".$ip."'
					)
			");
			$db->action("UPDATE images
					SET uniq_visitors = ".$uniq_visitors."
					WHERE uid = ".$uid."
			");
		}
		$string = visitors_.$lang;
		echo "<br>".$lang['visitors'].": ".$visits;
		if($options != '')
		{
			echo "<br>".$options;
		}
		//echo '</div>';
		//echo @'<form action="/">
		//		<input type="submit" value="Upload more">
		//		</form>
		//';
		echo '<br><a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-lang="'; echo $language.'"></a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</div>';
		$image_request = '1';
	}
	else
	{
		echo '<div id="image_not_exist">';
		echo '<center><div id="image_not_exist_inner">';
		echo $lang['image_not_exist'];
		echo '</div></center>';
		echo '</div>';
		header("Refresh: 5; url=/");
		exit;
	}
	$db->close();
}
else
{
	echo "<div class=\"main\">";
        
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
	

	/*echo '<div id="pic"></div><form action="upload.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="start();" name="upload">

	<div id="main" style="position:absolute; padding-top:5px; left:0px; right:0px; text-align:center;" align="center">
	<center>
	<div id="process"><img src="loader.gif"></div>
	<div id="upform"><div id="file_input_div">
	<input type="button" value="'; $string = upload_.$lang; echo $$string; echo'" id="file_input_button" style="font-size: 12px; left:0px; height:23px;" >
	<input class="file_input_hidden" type="file" name="pic" accept="image/*" onChange="document.upload.submitB.disabled=false; document.getElementById(\'file_input_button\').value = this.value; document.getElementById(\'submit\').style.visibility=\'visible\';" onmouseover="document.getElementById(\'file_input_button\').style.background=\'#91adc9\';" onmouseout="document.getElementById(\'file_input_button\').style.background=\'#6d8fb3\';">
	</div><br>';
	echo '<a style="font-size: 12px;">';
	$string = description_.$lang; echo $$string; echo ':</a> <input type="text" name="description" maxlength="100" style="border: 2px solid #d2d2d2; height:23px; font-size: 12px;"/><br><br>
	<input id="submit" type="submit" onclick="" value="'; $string = submit_.$lang; echo $$string; echo'" name="submitB" onMouseOver="this.style.background=\'#91adc9\';" onMouseOut="this.style.background=\'#6d8fb3\';" disabled></center></div>
    </div>

	<iframe id="upload_target" name="upload_target" src="#" style="display:none"></iframe>
	</form>'; */
}
//footer - flags

echo $close;


echo @'</div>';
echo '</div>
	</div>'; 

echo '<!-- Start of StatCounter Code -->
		<script type="text/javascript">
			var sc_project=7060543; 
			var sc_invisible=1; 
			var sc_security="9648f0e2"; 
		</script>

		<script type="text/javascript"
			src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
			class="statcounter"><a title="customizable counter"
			href="http://statcounter.com/free_hit_counter.html"
			target="_blank"><img class="statcounter"
			src="http://c.statcounter.com/7060543/0/9648f0e2/1/"
			alt="customizable counter" ></a></div></noscript>
	<!-- End of StatCounter Code -->
</div>';


	
?>


