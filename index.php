<?php
/*
 * Created on 20 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_user=$user['id_user'];
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	header('Content-Type: text/html');
else
	header('Content-Type: application/xhtml+xml');
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Photoalbum</title></head>
<body>
<h1>Photoalbum</h1>";

$sql = mysql_query("SELECT title, id_album FROM photoalbum_albums WHERE id_owner=$id_user ORDER BY title ASC");
echo "<p><strong>Mes albums :</strong><br/>";
if (mysql_num_rows($sql)!=0)
{
	while($row=mysql_fetch_assoc($sql))
	{
		$title=$row['title'];
		$id_album=$row['id_album'];
		echo "<a href='viewalbum.php?id_album=$id_album'>$title</a><br/>";
	}
}
echo "<br/><a href='newalbum.php'>Créer un nouvel album</a></p>";

$sql=mysql_query("SELECT title, id_album FROM photoalbum_albums WHERE id_album IN
		(SELECT p.id_album FROM photoalbum_photos AS p LEFT JOIN photoalbum_tags AS t ON t.id_photo=p.id_photo
		WHERE t.id_user=$id_user) ORDER BY title ASC");
if (mysql_num_rows($sql)!=0)
{
	echo "<p><strong>Albums où je suis :</strong><br/>";
	while($row=mysql_fetch_assoc($sql))
	{
		$title=$row['title'];
		$id_album=$row['id_album'];
		echo "<a href='viewalbum.php?id_album=$id_album'>$title</a><br/>";
	}
	echo "</p>";
}
if ($user['id_user']==-1)
	echo "<p><a href='login.php'>S'identifier</a></p>";
else
	echo "<p><a href='login.php?action=logout'>Se déconnecter</a></p>";
echo "</body>
</html>";

?>
