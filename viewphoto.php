<?php
/*
 * Created on 10 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_photo = intval($_REQUEST['id_photo']);
if (intval($_REQUEST['id_user'])>0)
	$id_user = intval($_REQUEST['id_user']);
	
if (!can_access_photo($user['id_user'], $id_photo))
	exit("Not authorized");
$sql=mysql_query("SELECT id_album, imgw FROM photoalbum_photos WHERE id_photo=$id_photo");
$row=mysql_fetch_assoc($sql);
$id_album=$row['id_album'];
$imgw=$row['imgw'];
$sql = mysql_query("SELECT u.name, t.x, t.y, t.height, t.width FROM photoalbum_tags AS t LEFT JOIN photoalbum_users AS u ON (t.id_user=u.id_user) WHERE t.id_photo = $id_photo ORDER BY u.name ASC");
$x = array();
$y = array();
$w = array();
$h = array();
$text = array();
$n = mysql_num_rows($sql);
while ($row=mysql_fetch_assoc($sql))
{
	$x[]=$row['x'];
	$y[]=$row['y'];
	$w[]=$row['width'];
	$h[]=$row['height'];
	$text[]=htmlspecialchars($row['name']);
}
$jvars='var n='.$n.';
var imgw='.$imgw.';
var x=['.implode(', ',$x).'];
var y=['.implode(', ',$y).'];
var w=['.implode(', ',$w).'];
var h=['.implode(', ',$h).'];
var text=["'.implode('", "',$text).'"];';

$sql=mysql_query("SELECT u.name, c.comment FROM photoalbum_comments AS c LEFT JOIN photoalbum_users AS u ON (u.id_user = c.id_user) WHERE c.id_photo=$id_photo ORDER BY c.id_comment ASC");
$comments=array();
while ($row=mysql_fetch_assoc($sql))
{
	$comments[]=$row;
}

if(isset($id_user))
	$sql=mysql_query("SELECT id_photo, id_album FROM photoalbum_photos WHERE id_photo IN
			(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user) AND ((id_album=$id_album AND id_photo>$id_photo) OR (id_album>$id_album)) ORDER BY id_album ASC, id_photo ASC");
else
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album AND id_photo>$id_photo ORDER BY id_photo ASC LIMIT 1");
	
if (mysql_num_rows($sql)!=0)
{
	while ($row = mysql_fetch_assoc($sql))
		if (can_access_photo($user['id_user'],$row['id_photo']))
		{
			$next = $row['id_photo'];
			break;
		}
}

if(isset($id_user))
	$sql=mysql_query("SELECT id_photo, id_album FROM photoalbum_photos WHERE id_photo IN
			(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user) AND ((id_album=$id_album AND id_photo<$id_photo) OR (id_album<$id_album)) ORDER BY id_album DESC, id_photo DESC");
else
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album AND id_photo<$id_photo ORDER BY id_photo DESC LIMIT 1");
	
if (mysql_num_rows($sql)!=0)
{
	while ($row = mysql_fetch_assoc($sql))
		if (can_access_photo($user['id_user'],$row['id_photo']))
		{
			$prev = $row['id_photo'];
			break;
		}
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	header('Content-Type: text/html');
else
	header('Content-Type: application/xhtml+xml');
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title>Visualisation d'une photo</title>
<link type='text/css' href='viewphoto.css' rel='stylesheet'/>
<script type='text/javascript'>
$jvars
</script>
<script type='text/javascript' src='viewphoto.js'></script>
</head>
<body onload='creatediv();'>
<h1>Visualisation d'une photo</h1>
<p>
<img id='photo' src='photo.php?id_photo=$id_photo' alt='photo' onmousemove='mouse(event);' ondblclick='resize();' width='800'/>
</p>
<p>";
if ($n > 0)
	echo "Sur cette photo : ";
foreach($text as $i => $name)
{
	echo "<span onmouseover='affcadre($i);' onmouseout='affcadre(n);'>$name</span>";
	if ($i < $n-1)
		echo ", ";
	else
		echo ".<br/>"; 
}
echo "<a href='tag.php?id_photo=$id_photo'>Taguer</a><br/>
</p>";

if(isset($id_user))
{
	$sql=mysql_query("SELECT name FROM photoalbum_users WHERE id_user=$id_user");
	$row = mysql_fetch_row($sql);
	echo "<p><a href='viewuser.php?id_user=$id_user'>Retour à l'album de $row[0]</a><br/>";
}
else
	echo "<p><a href='viewalbum.php?id_album=$id_album'>Retour à l'album</a><br/>";

echo "<a href='index.php'>Retour à l'accueil</a><br/>
</p>
<div class='navbox'>";
if (isset($id_user))
{
	if (isset($prev))
		echo "<div class='prev'><a href='viewphoto.php?id_photo=$prev&amp;id_user=$id_user'>Photo précedente</a></div>";
	if (isset($next))
		echo "<div class='next'><a href='viewphoto.php?id_photo=$next&amp;id_user=$id_user'>Photo suivante</a></div>";
} else {
	if (isset($prev))
		echo "<div class='prev'><a href='viewphoto.php?id_photo=$prev'>Photo précedente</a></div>";
	if (isset($next))
		echo "<div class='next'><a href='viewphoto.php?id_photo=$next'>Photo suivante</a></div>";
}
echo "</div>
<div class='commentbox'>Commentaires<br/>";
foreach($comments as $comment)
{
	echo "<div class='comment'>
<span class='writer'>$comment[name] : </span>
$comment[comment]
</div>";
}
echo "<form method='post' action='comment.php'>
<div class='commentinput'>
<textarea cols='80' rows='10' name='comment'></textarea><br/>
<input type='hidden' name='id_photo' value='$id_photo'/>
<input type='submit' value='Envoyer'/>
</div></form>
</div>
</body>
</html>"
?>
