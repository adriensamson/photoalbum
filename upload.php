<?php
/*
 * Created on 4 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("common.php");
$user = auth();

if ($user['id_user']==-1) exit("Not logged in");

if(!isset($_REQUEST['action']))
{
	$id_owner=$user['id_user'];
	$id_album=intval($_REQUEST['id_album']);
	$sql=mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album AND id_owner=$id_owner");
	if (mysql_num_rows($sql) == 0)
		exit("Not your album");
	$row = mysql_fetch_assoc($sql);
	$title=$row['title'];
	
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		header('Content-Type: text/html');
	else
		header('Content-Type: application/xhtml+xml');
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Ajout d'une photo</title></head>
<body>
<h1>Ajout d'une photo à l'album : $title</h1>
<p>
<a href='viewalbum.php?id_album=$id_album'>Visionner l'album</a>
</p>
<form method='post' action='upload.php' enctype='multipart/form-data'>
<p>
<input name='photo' type='file'/><br/>
<input type='hidden' name='id_album' value='$id_album'/>
<input type='hidden' name='action' value='upload'/>
<input type='submit' value='Ajouter'/>
</p></form>";
	
	if(isset($_REQUEST['last']))
	{
		$id_last = $_REQUEST['last'];
		echo "<p>Dernière photo envoyée :<br /><a href='viewphoto.php?id_photo=$id_last'><img src='photo.php?thumb=y&amp;id_photo=$id_last' alt='dernière photo'/></a></p>";
	}

	echo "</body>
</html>";
}
else
{
	$id_album = intval($_POST['id_album']);
	$id_owner=$user['id_user'];
	$sql = mysql_query("SELECT * FROM photoalbum_albums WHERE id_album=$id_album AND id_owner=$id_owner");
	if(mysql_num_rows($sql)==0) exit("Not your album");
	$filename = $id_album . '/' . basename($_FILES['photo']['name']);
	move_uploaded_file($_FILES['photo']['tmp_name'],$uploaddir.$filename);
	$image = new Imagick($uploaddir.$filename);
	$imgw = $image->getImageWidth();
	$image->thumbnailImage(100,100,true);
	$image->writeImage($thumbdir.$filename);
	
	$filename = mysql_real_escape_string($filename);
	mysql_query("INSERT INTO photoalbum_photos (filename, id_album, imgw) VALUES ('$filename', $id_album, $imgw)");
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE filename='$filename' AND id_album=$id_album");
	$row = mysql_fetch_assoc($sql);
	$id_photo = $row['id_photo'];
	log_newphoto($user['id_user'], $user['name'], $id_album, $id_photo);
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?last=$id_photo&id_album=$id_album";
	header("Location: $url");
}
?>
