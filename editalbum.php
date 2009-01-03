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
	
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_photoalbum = $xml_doc->createElement('photoalbum');
	$xml_doc->appendChild($xml_photoalbum);
	$xml_login = $xml_doc->createElement('login', $user['name']);
	$xml_photoalbum->appendChild($xml_login);
	$xml_title= $xml_doc->createElement('title', $title.' - Modification');
	$xml_photoalbum->appendChild($xml_title);
	$xml_idalbum = $xml_doc->createElement('idalbum', $id_album);
	$xml_photoalbum->appendChild($xml_idalbum);
	
	$xml_menuitem = $xml_doc->createElement('menuitem');
	$xml_title = $xml_doc->createElement('title', 'Accueil');
	$xml_menuitem->appendChild($xml_title);
	$xml_link = $xml_doc->createElement('link', 'index.php');
	$xml_menuitem->appendChild($xml_link);
	$xml_photoalbum->appendChild($xml_menuitem);
	
	$xml_menuitem = $xml_doc->createElement('menuitem');
	$xml_title = $xml_doc->createElement('title', $title);
	$xml_menuitem->appendChild($xml_title);
	$xml_link = $xml_doc->createElement('link', 'viewalbum.php?id_album='.$id_album);
	$xml_menuitem->appendChild($xml_link);
	$xml_photoalbum->appendChild($xml_menuitem);
	
	$xml_body = $xml_doc->createElement('body');
	$xml_body->setAttribute('page', 'editalbum');
	
	$xml_title = $xml_doc->createElement('title', $title);
	$xml_body->appendChild($xml_title);
	$xml_date = $xml_doc->createElement('date', $albumdate);
	
	$xml_guests = $xml_doc->createElement('guests');
	
	$sql = mysql_query("SELECT u.id_user, u.name FROM photoalbum_guests AS g LEFT JOIN photoalbum_users AS u ON (g.id_user=u.id_user) WHERE g.id_album=$id_album");
	while($row=mysql_fetch_assoc($sql))
	{
		$xml_guest = $xml_doc->createElement('guest');
		$xml_idguest = $xml_doc->createElement('idguest', $row['id_user']);
		$xml_guest->appendChild($xml_idguest);
		$xml_name = $xml_doc->createElement('name', $row['name']);
		$xml_guest->appendChild($xml_name);
		$xml_guests->appendChild($xml_guest);
	}
	$sql = mysql_query("SELECT name, id_user FROM photoalbum_users ORDER BY name ASC");
	while ($row=mysql_fetch_assoc($sql))
	{
		$xml_people = $xml_doc->createElement('people');
		$xml_id = $xml_doc->createElement('id', $row['id_user']);
		$xml_people->appendChild($xml_id);
		$xml_name = $xml_doc->createElement('name', $row['name']);
		$xml_people->appendChild($xml_name);
		$xml_guests->appendChild($xml_people);
	}
	$xml_body->appendChild($xml_guests);
	$xml_photoalbum->appendChild($xml_body);
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
elseif($_REQUEST['action']=='addguest')
{
	$id_guest = intval($_REQUEST['id_guest']);
	$sql = mysql_query("SELECT * FROM photoalbum_guests WHERE id_album=$id_album AND id_user=$id_guest");
	if(mysql_num_rows($sql)==0)
		mysql_query("INSERT INTO photoalbum_guests (id_album, id_user) VALUES ($id_album, $id_guest)");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
}
elseif($_REQUEST['action']=='deleteguest')
{
	$id_guest = intval($_REQUEST['id_guest']);
	mysql_query("DELETE FROM photoalbum_guests WHERE id_album=$id_album AND id_user=$id_guest");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
}
else
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
?>
