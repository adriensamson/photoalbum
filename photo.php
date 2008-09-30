<?php
/*
 * Created on 18 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
if (isset($_REQUEST['id_album']))
{
	$id_album = intval($_REQUEST['id_album']);
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album LIMIT 1");
	$row=mysql_fetch_assoc($sql);
	$id_photo = $row['id_photo'];
}
if (isset($_REQUEST['id_photo']))
	$id_photo = intval($_REQUEST['id_photo']);
if (isset($_REQUEST['thumb']))
	$dir = $thumbdir;
elseif (isset($_REQUEST['fullsize']))
	$dir = $uploaddir;
else
	$dir = $photodir;

if (!can_access_photo($user['id_user'], $id_photo))
	exit("Not authorized");

$sql = mysql_query("SELECT filename, lastchanged FROM photoalbum_photos WHERE id_photo=$id_photo");
$row = mysql_fetch_assoc($sql);
$filename = $dir . $row['filename'];

$headers = apache_request_headers();
if (isset($headers['If-Modified-Since']))
{
	$since = strtotime($headers['If-Modified-Since']);
	if ($since == $row['lastchanged'])
	{
		header('HTTP/1.0 304 Not Modified');
		exit;
	}
}

header("Last-Modified: ".gmdate('r', $row['lastchanged']));
header("Pragma: no-cache");
header("Content-Type: ".mime_content_type($filename));
readfile($filename);

?>
