<?php
/*
 * Created on 30 sept. 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_album = intval($_REQUEST['id_album']);
if (!is_owner($user['id_user'], $id_album))
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);

if(!isset($_REQUEST['action']))
{
	$sql = mysql_query("SELECT title, album_date FROM photoalbum_albums WHERE id_album=$id_album");
	$row = mysql_fetch_assoc($sql);
	$title = $row['title'];
	$albumdate = $row['album_date'];
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$title - Modification</title>
	<idalbum>$id_album</idalbum>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$title</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<body page='editalbum'>
		<title>$title</title>
		<date>$albumdate</date>
	</body>
</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'editalbum');
}
elseif($_REQUEST['action']=='delete')
{
	$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
	$row = mysql_fetch_row($sql);
	$title = $row[0];
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$title</title>
	<idalbum>$id_album</idalbum>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$title</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<body page='deletealbum'>
		<title>$title</title>
	</body>
</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'editalbum');
}
elseif($_REQUEST['action']=='confdelete')
{
	mysql_query("DELETE FROM photoalbum_comments WHERE id_photo IN (SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album)");
	mysql_query("DELETE FROM photoalbum_tags WHERE id_photo IN (SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album)");
	mysql_query("DELETE FROM photoalbum_photos WHERE id_album=$id_album");
	mysql_query("DELETE FROM photoalbum_albums WHERE id_album=$id_album");
	exec("rm -rf $uploaddir$id_album $thumbdir$id_album $photodir$id_album");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php');
}
elseif($_REQUEST['action']=='edit')
{
	$title=mysql_real_escape_string($_REQUEST['title']);
	$album_date=mysql_real_escape_string($_REQUEST['album_date']);
	mysql_query("UPDATE photoalbum_albums SET title='$title', album_date='$album_date' WHERE id_album=$id_album");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
}
else
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
?>
