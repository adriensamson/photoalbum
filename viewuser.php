<?php
/*
 * Created on 18 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("common.php");
$user = auth();
$id_user = intval($_REQUEST['id_user']);
$sql = mysql_query("SELECT name FROM photoalbum_users WHERE id_user=$id_user");
$row = mysql_fetch_assoc($sql);
$name = $row['name'];
$last_id_album=0;
$sql=mysql_query("SELECT id_photo, id_album FROM photoalbum_photos WHERE id_photo IN
			(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user) ORDER BY id_album ASC, id_photo ASC");

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	header('Content-Type: text/html');
else
	header('Content-Type: application/xhtml+xml');
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Visualisation des photos d'un utilisateur</title></head>
<body>
<h1>Visualisation des photos de : $name</h1>
<p>";
while ($row = mysql_fetch_assoc($sql))
{
	$id_photo=$row['id_photo'];
	$id_album=$row['id_album'];
	if (can_access_photo($user['id_user'], $id_photo))
	{
		if($id_album!=$last_id_album)
		{
			$last_id_album=$id_album;
			$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
			$row2 = mysql_fetch_assoc($sql);
			$title = $row2['title'];
			echo "</p>
			<p><h2>$title</h2><br />";
		}
		echo "<a href='viewphoto.php?id_photo=$id_photo'><img src='photo.php?id_photo=$id_photo&amp;thumb=y' alt='photo'/></a>&nbsp;";
	}
}
echo "</p>";
echo "<p>
<a href='index.php'>Retour à l'accueil</a>
</p>
</body>
</html>";

?>
