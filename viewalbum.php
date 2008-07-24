<?php
/*
 * Created on 18 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("common.php");
$user = auth();
$id_album = intval($_REQUEST['id_album']);
if (!can_access_album($user['id_user'], $id_album))
	exit("Not authorized");
$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
$row = mysql_fetch_assoc($sql);
$title = $row['title'];
$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album ORDER BY id_photo ASC");
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	header('Content-Type: text/html');
else
	header('Content-Type: application/xhtml+xml');
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Visualisation d'un album</title></head>
<body>
<h1>Visualisation d'un album : $title</h1>
<p>";
while ($row = mysql_fetch_assoc($sql))
{
	$id_photo=$row['id_photo'];
	echo "<a href='viewphoto.php?id_photo=$id_photo'><img src='photo.php?id_photo=$id_photo&amp;thumb=y' alt='photo'/></a>&nbsp;";
}
echo "</p>";
if (is_owner($user['id_user'], $id_album))
	echo "<p><a href='upload.php?id_album=$id_album'>Ajouter une photo</a></p>";
echo "<p>
<a href='index.php'>Retour à l'accueil</a>
</p>
</body>
</html>";

?>
