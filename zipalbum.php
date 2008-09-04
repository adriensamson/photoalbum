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
$file = tempnam("tmp", "zip");
   
$zip = new ZipArchive();

// Zip will open and overwrite the file, rather than try to read it.
$zip->open($file, ZipArchive::OVERWRITE);

    	$zip->addFromString('Album.txt', $title);
    	
while ($row = mysql_fetch_assoc($sql))
{
	$filename=$row['filename'];
	$zip->addFile('files/'.$filename, substr(strrchr($filename, "/"), 1 ));
	//echo 'files/'.$filename;
}
$zip->close();
// Stream the file to the client
header("Content-Type: application/zip");
header("Content-Length: " . filesize($file));
header("Content-Disposition: attachment; filename=\"Album_".$id_album.".zip\"");
readfile($file);

unlink($file); 

?>
