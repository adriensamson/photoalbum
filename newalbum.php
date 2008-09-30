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
	header('Content-Type: application/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/newalbum.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<title>Nouvel album</title>
	<body page='newalbum'/></photoalbum>";
}
else
{
	$title=mysql_real_escape_string($_REQUEST['title']);
	$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE title='$title'");
	if (mysql_num_rows($sql) != 0)
		exit("Title already taken");
	$id_owner=intval($user['id_user']);
	mysql_query("INSERT INTO photoalbum_albums (title, id_owner) VALUES ('$title', $id_owner)");
	$sql = mysql_query("SELECT id_album FROM photoalbum_albums WHERE title='$title'");
	$row = mysql_fetch_assoc($sql);
	$id_album = $row['id_album'];
	mkdir($uploaddir.$id_album);
	mkdir($thumbdir.$id_album);
	mkdir($photodir.$id_album);
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/newphoto.php?id_album=$id_album";
	header("Location: $url");
}
?>
