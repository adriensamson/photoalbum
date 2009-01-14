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
	$now=date('Y-m-d');
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<title>Nouvel album</title>
	<body page='newalbum'>
		<date>$now</date>
	</body>
</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'newalbum');
}
else
{
	$title=mysql_real_escape_string($_REQUEST['title']);
	$album_date=mysql_real_escape_string($_REQUEST['album_date']);
	$id_owner=intval($user['id_user']);
	mysql_query("INSERT INTO photoalbum_albums (title, id_owner, album_date) VALUES ('$title', $id_owner, '$album_date')");
	$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE title='$title' ORDER BY id_album DESC LIMIT 1");
	$row = mysql_fetch_assoc($sql);
	$id_album = $row['id_album'];
	mkdir($uploaddir.$id_album);
	mkdir($thumbdir.$id_album);
	mkdir($photodir.$id_album);
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/newphoto.php?id_album=$id_album";
	header("Location: $url");
}
?>
