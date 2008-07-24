<?php
/*
 * Created on 4 juil. 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include('../common.php');

$sql_logs = mysql_query("SELECT * FROM photoalbum_logs ORDER BY id_album ASC, logtime ASC");
$id_album = 0;
$message = array();
while ($row = mysql_fetch_assoc($sql_logs))
{
	if ($id_album != $row['id_album'])
	{
		$id_album = $row['id_album'];
		$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
		$r = mysql_fetch_assoc($sql);
		$title = $r['title'];
		$message[$id_album] = "\nDans l'album $title :\n";
	}
	$url = ($row['id_photo']) ? 'http://www.kyklydse.com/photoalbum/viewphoto.php?id_photo='.$row['id_photo'] : 'http://www.kyklydse.com/photoalbum/viewalbum.php?id_album='.$row['id_album'];
	$message[$id_album] .= "  * $row[logtime]: $row[log] $url\n";
	mysql_query("DELETE FROM photoalbum_logs WHERE id_log = $row[id_log]");
}
$mails = array();
foreach($message as $id_album => $msg)
{
	$sql = mysql_query("SELECT id_owner FROM photoalbum_albums WHERE id_album=$id_album");
	$row = mysql_fetch_assoc($sql);
	$list = array($row['id_owner'] => true);
	$sql = mysql_query("SELECT id_user FROM photoalbum_tags WHERE id_photo IN (SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album)");
	while ($row = mysql_fetch_assoc($sql))
		$list[] = $row['id_user'];
	$list = array_unique($list);
	foreach($list as $id_user)
	{
		if (!isset($mails[$id_user]))
			$mails[$id_user] = "Voici les dernières nouvelles de l'album photo :\n";
		$mails[$id_user] .= $msg;
	}
}
$header='Content-Type: text/plain; charset="UTF-8"\r\n';
$subject='Des nouvelles de l\'album photo';
foreach($mails as $id_user => $msg)
{
	$sql = mysql_query("SELECT invite, email FROM photoalbum_users WHERE id_user=$id_user");
	$row = mysql_fetch_assoc($sql);
	if (!$row['invite'])
		mail($row['email'], $subject, $msg, $header, '-f photoalbum@kyklydse.com');
}
?>
