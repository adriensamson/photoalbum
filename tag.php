<?php
/*
 * Created on 19 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
if ($user['id_user']==-1)
{
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}
if (!isset($_REQUEST['action']))
{
	$id_photo=intval($_REQUEST['id_photo']);
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		header('Content-Type: text/html');
	else
		header('Content-Type: application/xhtml+xml');
	$sql = mysql_query("SELECT imgw FROM photoalbum_photos WHERE id_photo = $id_photo");
	$row = mysql_fetch_assoc($sql);
	$imgw = $row['imgw'];
	$sql = mysql_query("SELECT name, id_user FROM photoalbum_users ORDER BY name ASC");
	$options = array();
	while ($row=mysql_fetch_assoc($sql))
		$options[$row['id_user']]=$row['name'];
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title>Tag</title>
<script type='text/javascript'>
var imgw = $imgw;
</script>
<script type='text/javascript' src='tag.js'></script>
</head>
<body onload='resize();'>
<h1>Tag</h1>
<p>Un premier clic pour le coin en haut à gauche, un deuxième pour le coin en bas à droite et les suivants pour redimensioner.<br/>
</p>
<form method='post' action='tag.php'>
<p>
<img id='photo' src='photo.php?id_photo=$id_photo' alt='image' onclick='clicked(event)'/><br/>
Nom : <select name='id_user'>";

	foreach($options as $id_user => $name)
		echo "<option value='$id_user'>$name</option>\n";

	echo "</select><a href='invite.php'> Ajouter quelqu'un</a><br/>
<input id='x' type='hidden' name='x'/>
<input id='y' type='hidden' name='y'/>
<input id='height' type='hidden' name='height'/>
<input id='width' type='hidden' name='width'/>
<input type='hidden' name='id_photo' value='$id_photo'/>
<input type='hidden' name='action' value='tag'/>
<input type='submit' value='Taguer'/>
</p>
<div id='rect' onclick='clicked(event)' ondblclick='resize();'/>
</form></body></html>";
}
elseif ($_REQUEST['action']=='tag')
{
	$id_photo=intval($_POST['id_photo']);
	$username=mysql_real_escape_string($_POST['name']);
	$id_user=intval($_REQUEST['id_user']);
	if (!can_access_photo($user['id_user'], $id_photo))
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	$id_tager=intval($user['id_user']);
	$x=intval($_POST['x']);
	$y=intval($_POST['y']);
	$height=intval($_POST['height']);
	$width=intval($_POST['width']);
	
	$sql = mysql_query("SELECT id_tag FROM photoalbum_tags WHERE id_user=$id_user AND id_photo=$id_photo");
	if (mysql_num_rows($sql)==0)
	{
		if ($x != -1) mysql_query("INSERT INTO photoalbum_tags (id_user, id_photo, x, y, height, width, id_tager) VALUES ($id_user, $id_photo, $x, $y, $height, $width, $id_tager)");
	}
	else
	{
		$result = mysql_fetch_assoc($sql);
		$id_tag = $result['id_tag'];
		if ($x != -1)
			mysql_query("UPDATE photoalbum_tags SET x=$x, y=$y, height=$height, width=$width, id_tager=$id_tager WHERE id_tag=$id_tag");
		else
			mysql_query("DELETE FROM photoalbum_tags WHERE id_tag=$id_tag");
	}
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/viewphoto.php?id_photo=$id_photo";
	header("Location: $url");
}
?>
