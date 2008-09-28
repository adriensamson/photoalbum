<?php
/*
 * Created on 10 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$uploaddir = "files/";
$thumbdir = "thumbs/";
$photodir = "photos/";
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

function select_is_owner_album($id_user)
{
	return "SELECT id_album FROM photoalbum_albums WHERE id_owner=$id_user";
}
function select_is_owner_photo($id_user)
{
	$subsql = select_is_owner_album($id_user);
	return "SELECT id_photo FROM photoalbum_photos WHERE id_album IN ($subsql)";
}
function select_can_access_album($id_user)
{
	$is_owner = select_is_owner_album($id_user);
	return "SELECT id_album FROM photoalbum_albums WHERE (id_album IN ($is_owner) OR id_album IN
		(SELECT id_album FROM photoalbum_photos WHERE id_photo IN
		(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user)))";
}
function select_can_access_photo($id_user)
{
	$subsql = select_can_access_album($id_user);
	return "SELECT id_photo FROM photoalbum_photos WHERE id_album IN ($subsql)";
}
function select_whois_in_photo($id_photo)
{
	return "SELECT id_user FROM photoalbum_tags WHERE id_photo = $id_photo";
}
function select_whois_in_album($id_album)
{
	return "SELECT id_user FROM photoalbum_tags WHERE id_photo IN
		(SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album)";
}

function can_access_photo($id_user, $id_photo)
{
	$sql = mysql_query(select_can_access_photo($id_user)." AND id_photo=$id_photo");
	if (mysql_num_rows($sql) != 0) return true;
	else return false;
}

function is_owner($id_user, $id_album)
{
	$sql = mysql_query(select_is_owner_album($id_user)." AND id_album = $id_album");
	if (mysql_num_rows($sql) != 0) return true;
	else return false;
}

function can_access_album($id_user, $id_album)
{
	$sql = mysql_query(select_can_access_album($id_user)." AND id_album=$id_album");
	if (mysql_num_rows($sql) != 0) return true;
	else return false;
}

function get_unseen($id_user, $album = false)
{
	if ($id_user==-1)
		return array();
	$sql = mysql_query("SELECT lastvisit FROM photoalbum_users WHERE id_user=$id_user");
	$row = mysql_fetch_row($sql);
	$lastvisit = $row[0];
	$canaccess = select_can_access_photo($id_user);
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_photo IN ($canaccess) AND lastchanged > $lastvisit");
	while($row = mysql_fetch_row($sql))
	{
		mysql_query("INSERT INTO photoalbum_unseen_changes (id_user, id_photo) VALUES ($id_user, $row[0])");
	}
	$now = time();
	mysql_query("UPDATE photoalbum_users SET lastvisit = $now WHERE id_user=$id_user");
	$unseen = array();
	if(!$album)
		$sql = mysql_query("SELECT id_photo FROM photoalbum_unseen_changes WHERE id_user=$id_user");
	else
		$sql = mysql_query("SELECT p.id_album FROM photoalbum_unseen_changes AS c LEFT JOIN photoalbum_photos AS p ON (c.id_photo = p.id_photo) WHERE c.id_user=$id_user)");
	while($row = mysql_fetch_row($sql))
	{
		$unseen[$row[0]]=true;
	}
}

function set_seen($id_user, $id_photo)
{
	if ($id_user != -1)
		mysql_query("DELETE FROM photoalbum_unseen_changes WHER id_user=$id_user AND id_photo=$id_photo");
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
