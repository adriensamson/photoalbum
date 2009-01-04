<?php
/*
 * Created on 30 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_user = $user['id_user'];
$comment = mysql_real_escape_string($_REQUEST['comment']);
$id_photo = intval($_REQUEST['id_photo']);
if ($user['id_user']==-1 || !can_access_photo($id_user, $id_photo))
{
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}

mysql_query("INSERT INTO photoalbum_comments (id_user, id_photo, comment) VALUES ($id_user, $id_photo, '$comment')");
$now = time();
mysql_query("UPDATE photoalbum_photos SET lastchanged=$now WHERE id_photo = $id_photo");
$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE id_album IN (SELECT id_album FROM photoalbum_photos WHERE id_photo=$id_photo)");
$row = mysql_fetch_assoc($sql);
$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/viewphoto.php?id_photo=$id_photo";
header("Location: $url");

?>
