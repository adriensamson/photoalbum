<?php
/*
 * Created on 19 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();

if ($user['id_user']==-1) exit("Not logged in");

if (!isset($_REQUEST['action']))
{
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		header('Content-Type: text/html');
	else
		header('Content-Type: application/xhtml+xml');
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Création d'un album</title></head>
<body>
<h1>Création d'un album</h1>
<form method='post' action='newalbum.php'>
<p>
Nom de l'album : <input name='title'/><br/>
<input type='submit' value='Créer'/>
<input type='hidden' name='action' value='create'/>
</p>
</form>
</body>
</html>";
}
else
{
	$title=mysql_real_escape_string($_REQUEST['title']);
	$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE title='$title'");
	if (mysql_num_rows($sql) != 0)
		exit("Title already taken");
	$id_owner=intval($user['id_user']);
	mysql_query("INSERT INTO photoalbum_albums (title, id_owner) VALUES ('$title', $id_owner)");
	$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE title='$title'");
	$row = mysql_fetch_assoc($sql);
	$id_album = $row['id_album'];
	mkdir($uploaddir.$id_album);
	mkdir($thumbdir.$id_album);
	mkdir($photodir.$id_album);
	log_newalbum($user['id_user'], $user['name'], $id_album);
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/upload.php?id_album=$id_album";
	header("Location: $url");
}
?>
