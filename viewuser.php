<?php
/*
 * Created on 18 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("common.php");
$user = auth();
$id_user = intval($_REQUEST['id_user']);
$sql = mysql_query("SELECT name FROM photoalbum_users WHERE id_user=$id_user");
$row = mysql_fetch_assoc($sql);
$name = $row['name'];

$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/viewuser.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$name</title>
	<iduser>$id_user</iduser>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<body page='viewuser'>";
	

$can_access = select_can_access_photo($user['id_user']);
$last_id_album=-1;
$sql=mysql_query("SELECT id_photo, id_album FROM photoalbum_photos WHERE id_photo IN
			(SELECT id_photo FROM photoalbum_tags WHERE id_user=$id_user) AND id_photo IN ($can_access) ORDER BY id_album ASC, id_photo ASC");
while ($row = mysql_fetch_assoc($sql))
{
	$id_photo=$row['id_photo'];
	$id_album=$row['id_album'];
	$albumtitle=$row['title'];
	if($id_album!=$last_id_album)
	{
		if ($last_id_album!=-1)
			$xml_str .= "</album>";
		$sql2 = mysql_query("SELECT a.title, u.name FROM photoalbum_albums AS a LEFT JOIN photoalbum_users AS u ON (a.id_owner=u.id_user) WHERE a.id_album=$id_album");
		$row2 = mysql_fetch_assoc($sql2);
		$xml_str .= "<album><id>$id_album</id><title>$row2[title]</title><author>$row2[name]</author>";
		$last_id_album=$id_album;
	}
	$xml_str .= "<photo><id>$id_photo</id>";
	$sql2 = mysql_query("SELECT COUNT(*) FROM photoalbum_comments WHERE id_photo=$id_photo");
	$row2 = mysql_fetch_row($sql2);
	$xml_str .= "<nbcomments>$row2[0]</nbcomments><peoples>";
	$whosin = select_whois_in_photo($id_photo);
	$sql2 = mysql_query("SELECT id_user, name FROM photoalbum_users WHERE id_user IN ($whosin) ORDER BY name ASC");
	while ($row2=mysql_fetch_assoc($sql2))
		$xml_str .= "<people><id>$row2[id_user]</id><name>$row2[name]</name></people>";
	$xml_str .= "</peoples></photo>";
}
if($id_last_album!=-1)
	$xml_str .= "</album>";
$xml_str .= "</body></photoalbum>";
$xml_doc = new DOMDocument('1.0', 'UTF-8');
$xml_doc->loadXML($xml_str);
render($xml_doc, 'viewuser');

?>
