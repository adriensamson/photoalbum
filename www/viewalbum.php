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

if(isset($_REQUEST['markseen']))
	set_all_seen($user['id_user'], $id_album);
$unseen = get_unseen($user['id_user']);
$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
$row = mysql_fetch_assoc($sql);
$title = $row['title'];
$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album ORDER BY id_photo ASC");

$xml_doc = new DOMDocument('1.0', 'UTF-8');
$xml_photoalbum = $xml_doc->createElement('photoalbum');
$xml_doc->appendChild($xml_photoalbum);
$xml_login = $xml_doc->createElement('login', $user['name']);
$xml_photoalbum->appendChild($xml_login);
$xml_title = $xml_doc->createElement('title', $title);
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
$xml_link = $xml_doc->createElement('link', "viewalbum.php?id_album=$id_album");
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_title = $xml_doc->createElement('title', 'Archive ZIP');
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', "album_$id_album.zip");
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

if (is_owner($user['id_user'], $id_album))
{
	$xml_owner = $xml_doc->createElement('owner');
	$xml_photoalbum->appendChild($xml_owner);
}

$xml_body = $xml_doc->createElement('body');
$xml_body->setAttribute('page', 'viewalbum');

while ($row = mysql_fetch_assoc($sql))
{
	$id_photo=$row['id_photo'];
	$sql2 = mysql_query("SELECT COUNT(*) FROM photoalbum_comments WHERE id_photo=$id_photo");
	$row2 = mysql_fetch_row($sql2);
	$nbcomments = $row2[0];
	$whois = select_whois_in_photo($id_photo);
	$sql2 = mysql_query("SELECT id_user, name FROM photoalbum_users WHERE id_user IN ($whois) ORDER BY name ASC");
	$xml_photo = $xml_doc->createElement('photo');
	$xml_id = $xml_doc->createElement('id', $id_photo);
	$xml_photo->appendChild($xml_id);
	$xml_nbcomments = $xml_doc->createElement('nbcomments', $nbcomments);
	$xml_photo->appendChild($xml_nbcomments);
	if (isset($unseen[$id_photo]))
	{
		$xml_changed = $xml_doc->createElement('changed');
		$xml_photo->appendChild($xml_changed);
	}
	$xml_peoples = $xml_doc->createElement('peoples');
	while ($row2=mysql_fetch_assoc($sql2))
	{
		$xml_people = $xml_doc->createElement('people');
		$xml_id = $xml_doc->createElement('id', $row2['id_user']);
		$xml_people->appendChild($xml_id);
		$xml_name = $xml_doc->createElement('name', $row2['name']);
		$xml_people->appendChild($xml_name);
		$xml_peoples->appendChild($xml_people);
	}
	$xml_photo->appendChild($xml_peoples);
	$xml_body->appendChild($xml_photo);
}
$xml_photoalbum->appendChild($xml_body);

render($xml_doc, 'viewalbum');

?>
