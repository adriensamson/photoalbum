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
$sql = mysql_query("SELECT filename FROM photoalbum_photos WHERE id_album=$id_album ORDER BY id_photo ASC");
    	

//Création du fichier zip temporaire
$file = realpath($uploaddir).'/'.$id_album.'.zip';
function onexit($f)
{
	unlink($f);
}
register_shutdown_function('onexit', $file);
exec("cd $uploaddir; zip -r0 $id_album $id_album");

header("Content-Type: application/zip");
header("Content-Length: ".filesize("$uploaddir$id_album.zip"));
readfile("$uploaddir$id_album.zip");
unlink($file);
?>
