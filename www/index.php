<?php
/*
 * Created on 20 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_user=$user['id_user'];
if($user['id_user']==-1)
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/login.php');

if(isset($_REQUEST['markseen']))
	set_all_seen($id_user);
$unseen = get_unseen($user['id_user'], true);

$xml_doc = new DOMDocument('1.0', 'UTF-8');
$xml_photoalbum = $xml_doc->createElement('photoalbum');
$xml_doc->appendChild($xml_photoalbum);
$xml_login = $xml_doc->createElement('login', $user['name']);
$xml_photoalbum->appendChild($xml_login);
$xml_title = $xml_doc->createElement('title', 'Accueil');
$xml_photoalbum->appendChild($xml_title);
$xml_menuitem = $xml_doc->createElement('menuitem');
$xml_photoalbum->appendChild($xml_menuitem);
$xml_title = $xml_doc->createElement('title', 'Accueil');
$xml_menuitem->appendChild($xml_title);
$xml_link = $xml_doc->createElement('link', 'index.php');
$xml_menuitem->appendChild($xml_link);
$xml_body = $xml_doc->createElement('body');
$xml_body->setAttribute('page', 'index');

$can_access=select_can_access_album($id_user);
$sql=mysql_query("SELECT title, id_album, id_owner FROM photoalbum_albums WHERE id_album IN
		($can_access) ORDER BY album_date DESC");
while($row=mysql_fetch_assoc($sql))
{
	$title=$row['title'];
	$id_album=$row['id_album'];
	$sql2 = mysql_query("SELECT name FROM photoalbum_users WHERE id_user=$row[id_owner]");
	$row2 = mysql_fetch_assoc($sql2);
	$author=$row2['name'];
	$sql2 = mysql_query("SELECT COUNT(*) FROM photoalbum_photos WHERE id_album=$id_album");
	$row2 = mysql_fetch_row($sql2);
	$nbphotos=$row2[0];
	$whois = select_whois_in_album($id_album);
	$sql2 = mysql_query("SELECT id_user, name FROM photoalbum_users WHERE id_user IN ($whois) ORDER BY name ASC");
	
	$xml_album = $xml_doc->createElement('album');
	$xml_id = $xml_doc->createElement('id', $id_album);
	$xml_album->appendChild($xml_id);
	$xml_name = $xml_doc->createElement('name', $title);
	$xml_album->appendChild($xml_name);
	$xml_author = $xml_doc->createElement('author', $author);
	$xml_album->appendChild($xml_author);
	$xml_nbphotos = $xml_doc->createElement('nbphotos', $nbphotos);
	$xml_album->appendChild($xml_nbphotos);
	if ($row['id_owner']==$user['id_user'])
	{
		$xml_owner = $xml_doc->createElement('owner');
		$xml_album->appendChild($xml_owner);
	}
	
	if (isset($unseen[$id_album]))
	{
		$xml_changed = $xml_doc->createElement('changed');
		$xml_album->appendChild($xml_changed);
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
	$xml_album->appendChild($xml_peoples);
	$xml_body->appendChild($xml_album);
}

$xml_photoalbum->appendChild($xml_body);

render($xml_doc, 'index');
?>
