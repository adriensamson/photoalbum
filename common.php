<?php
/*
 * Created on 10 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$uploaddir = "files/";
$thumbdir = "thumbs/";
include('secret.php');
mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_db);
unset($db_host, $db_user, $db_pass, $db_db);

function auth()
{
	if (isset($_COOKIE['photoalbum']))
	{
		$user_array = unserialize($_COOKIE['photoalbum']);
		$id_user = intval($user_array['id_user']);
		$md5 = mysql_real_escape_string($user_array['md5']);
		$sql = mysql_query("SELECT id_user, name FROM photoalbum_users WHERE id_user=$id_user AND md5='$md5'");
		if (mysql_num_rows($sql)==1)
			return mysql_fetch_assoc($sql);
		setcookie('photoalbum','');
	}
	return array("id_user" => -1, 'name' => "Anonymous");
}

function can_access_photo($id_user, $id_photo)
{
	$sql = mysql_query("SELECT COUNT(*) FROM photoalbum_photos WHERE id_photo=$id_photo AND id_album IN
		(SELECT id_album FROM photoalbum_albums WHERE id_owner=$id_user)");
	$result = mysql_fetch_row($sql);
	if ($result[0] != 0) return true;
	$sql = mysql_query("SELECT COUNT(*) FROM photoalbum_photos WHERE id_photo=$id_photo AND id_album IN
		(SELECT id_album FROM photoalbum_photos WHERE id_photo IN
		(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user))");
	$result = mysql_fetch_row($sql);
	if ($result[0] != 0) return true;
	else return false;
}

function is_owner($id_user, $id_album)
{
	$sql = mysql_query("SELECT COUNT(*) FROM photoalbum_albums AS a WHERE a.id_owner=$id_user AND a.id_album = $id_album");
	$result = mysql_fetch_row($sql);
	if ($result[0] != 0) return true;
}

function can_access_album($id_user, $id_album)
{
	$sql = mysql_query("SELECT COUNT(*) FROM photoalbum_albums AS a WHERE a.id_album=$id_album AND a.id_owner=$id_user");
	$result = mysql_fetch_row($sql);
	if ($result[0] != 0) return true;
	$sql = mysql_query("SELECT COUNT(*) FROM photoalbum_albums WHERE id_album=$id_album AND id_album IN
		(SELECT id_album FROM photoalbum_photos WHERE id_photo IN
		(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user))");
	$result = mysql_fetch_row($sql);
	if ($result[0] != 0) return true;
	else return false;
}

function log_newalbum($id_user, $username, $id_album)
{
	$date = date('Y-m-d H:i:s');
	$log = mysql_real_escape_string("Création de l'album par $username");
	mysql_query("INSERT INTO photoalbum_logs (logtime, id_user, id_album, log) VALUES ('$date', $id_user, $id_album, '$log')");
}

function log_newphoto($id_user, $username, $id_album, $id_photo)
{
	$date = date('Y-m-d H:i:s');
	$log = mysql_real_escape_string("Nouvelle photo de $username");
	mysql_query("INSERT INTO photoalbum_logs (logtime, id_user, id_album, id_photo, log) VALUES ('$date', $id_user, $id_album, $id_photo, '$log')");
}

function log_newcomment($id_user, $username, $id_album, $id_photo)
{
	$date = date('Y-m-d H:i:s');
	$log = mysql_real_escape_string("Nouveau commentaire de $username");
	mysql_query("INSERT INTO photoalbum_logs (logtime, id_user, id_album, id_photo, log) VALUES ('$date', $id_user, $id_album, $id_photo, '$log')");
}
?>
