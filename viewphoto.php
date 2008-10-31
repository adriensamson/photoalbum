<?php
/*
 * Created on 10 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_photo = intval($_REQUEST['id_photo']);
set_seen($user['id_user'], $id_photo);
if (intval($_REQUEST['id_user'])>0)
{
	$id_user = intval($_REQUEST['id_user']);
	$sql = mysql_query("SELECT name FROM photoalbum_users WHERE id_user=$id_user");
	if ($row = mysql_fetch_assoc($sql))
		$id_user_name = $row['name'];
	else
		unset($id_user);
}	
if (!can_access_photo($user['id_user'], $id_photo))
	exit("Not authorized");
$sql=mysql_query("SELECT a.id_album, a.title FROM photoalbum_photos AS p LEFT JOIN photoalbum_albums AS a ON (a.id_album=p.id_album) WHERE p.id_photo=$id_photo");
$row=mysql_fetch_assoc($sql);
$id_album=$row['id_album'];
$albumtitle=$row['title'];

$xml_doc = new DOMDocument('1.0', 'UTF-8');
$xml_photoalbum = $xml_doc->createElement('photoalbum');
$xml_doc->appendChild($xml_photoalbum);
$xml_login = $xml_doc->createElement('login', $user['name']);
$xml_photoalbum->appendChild($xml_login);
$xml_title = $xml_doc->createElement('title', "$albumtitle - Photo");
$xml_photoalbum->appendChild($xml_title);
$xml_idphoto = $xml_doc->createElement('idphoto', $id_photo);
$xml_photoalbum->appendChild($xml_idphoto);

$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_title = $xml_doc->createElement('title', 'Accueil');
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', 'index.php');
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_title = $xml_doc->createElement('title', $albumtitle);
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', "viewalbum.php?id_album=$id_album");
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_title = $xml_doc->createElement('title', 'Taille réelle');
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', "photo_fullsize_$id_photo.jpg");
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_title = $xml_doc->createElement('title', 'Taguer');
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', "tag.php?id_photo=$id_photo");
$xml_menuitem->appendChild($xml_link);
$xml_photoalbum->appendChild($xml_menuitem);

if (is_owner($user['id_user'], $id_album))
{
	$xml_owner = $xml_doc->createElement('owner');
	$xml_photoalbum->appendChild($xml_owner);
}

if (isset($id_user))
{
	$xml_menuitem = $xml_doc->createElement('menuitem');
	$xml_title = $xml_doc->createElement('title', $id_user_name);
	$xml_menuitem->appendChild($xml_title);
	$xml_link = $xml_doc->createElement('link', "viewuser.php?id_user=$id_user");
	$xml_menuitem->appendChild($xml_link);
	$xml_photoalbum->appendChild($xml_menuitem);

	$select_can_access = select_can_access_photo($user['id_user']);
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos
		WHERE ((id_album = $id_album AND id_photo > $id_photo) OR id_album > $id_album)
		AND id_photo IN ($select_can_access)
		AND id_photo IN (SELECT id_photo FROM photoalbum_tags WHERE id_user = $id_user)
		ORDER BY id_album ASC, id_photo ASC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
	{
		$xml_next = $xml_doc->createElement('next', "viewphoto.php?id_photo=$row[id_photo]&amp;id_user=$id_user");
		$xml_photoalbum->appendChild($xml_next);
	}
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos
		WHERE ((id_album = $id_album AND id_photo < $id_photo) OR id_album < $id_album)
		AND id_photo IN ($select_can_access)
		AND id_photo IN (SELECT id_photo FROM photoalbum_tags WHERE id_user = $id_user)
		ORDER BY id_album DESC, id_photo DESC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
	{
		$xml_prev = $xml_doc->createElement('prev', "viewphoto.php?id_photo=$row[id_photo]&amp;id_user=$id_user");
		$xml_photoalbum->appendChild($xml_prev);
	}
}
else
{
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album = $id_album AND id_photo > $id_photo ORDER BY id_photo ASC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
	{
		$xml_next = $xml_doc->createElement('next', "viewphoto.php?id_photo=$row[id_photo]");
		$xml_photoalbum->appendChild($xml_next);
	}
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album = $id_album AND id_photo < $id_photo ORDER BY id_photo DESC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
	{
		$xml_prev = $xml_doc->createElement('prev', "viewphoto.php?id_photo=$row[id_photo]");
		$xml_photoalbum->appendChild($xml_prev);
	}
}

$xml_body = $xml_doc->createElement('body');
$xml_body->setAttribute('page', 'viewphoto');

$sql = mysql_query("SELECT u.id_user, u.name, t.x, t.y, t.height, t.width FROM photoalbum_tags AS t LEFT JOIN photoalbum_users AS u ON (t.id_user=u.id_user) WHERE t.id_photo = $id_photo ORDER BY u.name ASC");
while ($row=mysql_fetch_assoc($sql))
{
	$xml_cadre = $xml_doc->createElement('cadre');
	$xml_cadre->setAttribute('x', $row['x']);
	$xml_cadre->setAttribute('y', $row['y']);
	$xml_cadre->setAttribute('h', $row['height']);
	$xml_cadre->setAttribute('w', $row['width']);
	$xml_people = $xml_doc->createElement('people');
	$xml_id = $xml_doc->createElement('id', $row['id_user']);
	$xml_people->appendChild($xml_id);
	$xml_name = $xml_doc->createElement('name', $row['name']);
	$xml_people->appendChild($xml_name);
	$xml_cadre->appendChild($xml_people);
	$xml_body->appendChild($xml_cadre);
}

$sql=mysql_query("SELECT u.id_user, u.name, c.comment FROM photoalbum_comments AS c LEFT JOIN photoalbum_users AS u ON (u.id_user = c.id_user) WHERE c.id_photo=$id_photo ORDER BY c.id_comment ASC");
while ($row=mysql_fetch_assoc($sql))
{
	$xml_comment = $xml_doc->createElement('comment');
	$xml_people = $xml_doc->createElement('people');
	$xml_id = $xml_doc->createElement('id', $row['id_user']);
	$xml_people->appendChild($xml_id);
	$xml_name = $xml_doc->createElement('name', $row['name']);
	$xml_people->appendChild($xml_name);
	$xml_comment->appendChild($xml_people);
	$xml_text = $xml_doc->createElement('text', $row['comment']);
	$xml_comment->appendChild($xml_text);
	$xml_body->appendChild($xml_comment);
}
$xml_photoalbum->appendChild($xml_body);

render($xml_doc, 'viewphoto');

?>
