<?php
/*
 * Created on 18 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_photo = intval($_REQUEST['id_photo']);
$dir = (isset($_REQUEST['thumb'])) ? $thumbdir : $uploaddir;
if (!can_access_photo($user['id_user'], $id_photo))
	exit("Not authorized");

$sql = mysql_query("SELECT filename FROM photoalbum_photos WHERE id_photo=$id_photo");
$row = mysql_fetch_assoc($sql);
$filename = $dir . $row['filename'];

header("Expires: ".gmdate('r', time()+3600*24*365));
header("Cache-Control: private");
header("Content-Type: ".mime_content_type($filename));
readfile($filename);

?>
