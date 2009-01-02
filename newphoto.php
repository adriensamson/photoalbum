<?php
/*
 * Created on 4 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
include("common.php");
$user = auth();

if ($user['id_user']==-1) exit("Not logged in");

if(!isset($_REQUEST['action']))
{
	$id_owner=$user['id_user'];
	$id_album=intval($_REQUEST['id_album']);
	$sql=mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album AND id_owner=$id_owner");
	if (mysql_num_rows($sql) == 0)
		exit("Not your album");
	$row = mysql_fetch_assoc($sql);
	$title=$row['title'];
	
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$title - Nouvelle photo</title>
	<idalbum>$id_album</idalbum>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$title</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<body page='newphoto'/>";
	if(isset($_REQUEST['last']))
	{
		$id_last = $_REQUEST['last'];
		$sql = mysql_query("SELECT filename FROM photoalbum_photos WHERE id_photo=$id_last");
		$row = mysql_fetch_assoc($sql);
		$filename = basename($row['filename']);
		$xml_str .= "<lastphoto><id>$id_last</id><filename>$filename</filename></lastphoto>";
	}
	$xml_str .= "</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'newphoto');
}
else
{
	$id_album = intval($_POST['id_album']);
	$id_owner=$user['id_user'];
	$sql = mysql_query("SELECT * FROM photoalbum_albums WHERE id_album=$id_album AND id_owner=$id_owner");
	if(mysql_num_rows($sql)==0) exit("Not your album");
	$filename = $id_album . '/' . basename($_FILES['photo']['name']);
	move_uploaded_file($_FILES['photo']['tmp_name'],$uploaddir.$filename);
	system('convert '.escapeshellarg($uploaddir.$filename).' -thumbnail 100x100 '.escapeshellarg($thumbdir.$filename).' &');
	system('convert '.escapeshellarg($uploaddir.$filename).' -resize 800x800 '.escapeshellarg($photodir.$filename).' &');
	$filename = mysql_real_escape_string($filename);
	$now = time();
	$legend = mysql_real_escape_string($_REQUEST['legend']);
	mysql_query("INSERT INTO photoalbum_photos (filename, id_album, lastchanged, legend) VALUES ('$filename', $id_album, $now, '$legend')");
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE filename='$filename' AND id_album=$id_album");
	$row = mysql_fetch_assoc($sql);
	$id_photo = $row['id_photo'];
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?last=$id_photo&id_album=$id_album";
	header("Location: $url");
}
?>
