<?php
/*
 * Created on 30 sept. 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
$id_photo = intval($_REQUEST['id_photo']);
$sql=mysql_query("SELECT a.id_album, a.title FROM photoalbum_photos AS p LEFT JOIN photoalbum_albums AS a ON (a.id_album=p.id_album) WHERE p.id_photo=$id_photo");
$row=mysql_fetch_assoc($sql);
$id_album=$row['id_album'];
$title=$row['title'];
if (!is_owner($user['id_user'], $id_album))
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewphoto.php?id_photo='.$id_photo);

if(!isset($_REQUEST['action']))
{
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$title - Photo - Modification</title>
	<idphoto>$id_photo</idphoto>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$title</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<menuitem>
		<title>Photo</title>
		<link>viewphoto.php?id_photo=$id_photo</link>
	</menuitem>
	<body page='editphoto'>";
	
	$sql = mysql_query("SELECT u.name, t.id_tag, t.fake_tag FROM photoalbum_tags AS t LEFT JOIN photoalbum_users AS u ON (t.id_user=u.id_user) WHERE t.id_photo=$id_photo");
	while($row=mysql_fetch_assoc($sql))
	{
		$name = ($row['name']) ? $row['name'] : $row['fake_tag'];
		$xml_str .= "<tag><idtag>$row[id_tag]</idtag><name>$name</name></tag>";
	}
	$xml_str .= "</body>
</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'editphoto');
}
elseif($_REQUEST['action']=='delete')
{
	header('Content-Type: application/xml');
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$title - Photo - Modification</title>
	<idphoto>$id_photo</idphoto>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$title</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<menuitem>
		<title>Photo</title>
		<link>viewphoto.php?id_photo=$id_photo</link>
	</menuitem>
	<body page='deletephoto'/>
</photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'editphoto');
}
elseif($_REQUEST['action']=='confdelete')
{
	mysql_query("DELETE FROM photoalbum_comments WHERE id_photo=$id_photo");
	mysql_query("DELETE FROM photoalbum_tags WHERE id_photo=$id_photo");
	$sql = mysql_query("SELECT filename FROM photoalbum_photos WHERE id_photo=$id_photo");
	$row = mysql_fetch_assoc($sql);
	$filename = $row[0];
	mysql_query("DELETE FROM photoalbum_photos WHERE id_photo=$id_photo");
	@unlink("$uploaddir$id_album/$filename");
	@unlink("$thumbdir$id_album/$filename");
	@unlink("$photodir$id_album/$filename");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewalbum.php?id_album='.$id_album);
}
elseif($_REQUEST['action']=='deletetag')
{
	$id_tag=intval($_REQUEST['id_tag']);
	mysql_query("DELETE FROM photoalbum_tags WHERE id_photo=$id_photo AND id_tag=$id_tag");
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewphoto.php?id_photo='.$id_photo);
}
elseif($_REQUEST['action']=='rotateleft')
{
	$sql = mysql_query("SELECT filename, id_album FROM photoalbum_photos WHERE id_photo=$id_photo");
	$row = mysql_fetch_assoc($sql);
	$filename = $row['filename'];
	$imagesize = getimagesize($photodir.$filename);
	system('convert '.escapeshellarg($uploaddir.$filename).' -rotate -90 '.escapeshellarg($uploaddir.$filename).' &');
	system('convert '.escapeshellarg($thumbdir.$filename).' -rotate -90 '.escapeshellarg($thumbdir.$filename).' &');
	system('convert '.escapeshellarg($photodir.$filename).' -rotate -90 '.escapeshellarg($photodir.$filename).' &');
	$now = time();
	mysql_query("UPDATE photoalbum_photos SET lastchanged=$now WHERE id_photo=$id_photo");
	
	$sql = mysql_query("SELECT id_tag, id_photo, x, y, width, height FROM photoalbum_tags WHERE id_photo=$id_photo");
	while($row=mysql_fetch_assoc($sql))
	{
		$width = $row['height'];
		$height = $row['width'];
		$x = $row['y'];
		$y = $imagesize[0] - $row['x'] - $row['width'];
		mysql_query("UPDATE photoalbum_tags SET x=$x, y=$y, width=$width, height=$height WHERE id_tag=$row[id_tag]");		
	}
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewphoto.php?id_photo='.$id_photo);
}
elseif($_REQUEST['action']=='rotateright')
{
	$sql = mysql_query("SELECT filename, id_album FROM photoalbum_photos WHERE id_photo=$id_photo");
	$row = mysql_fetch_assoc($sql);
	$filename = $row['filename'];
	$imagesize = getimagesize($photodir.$filename);
	system('convert '.escapeshellarg($uploaddir.$filename).' -rotate 90 '.escapeshellarg($uploaddir.$filename).' &');
	system('convert '.escapeshellarg($thumbdir.$filename).' -rotate 90 '.escapeshellarg($thumbdir.$filename).' &');
	system('convert '.escapeshellarg($photodir.$filename).' -rotate 90 '.escapeshellarg($photodir.$filename).' &');
	$now = time();
	mysql_query("UPDATE photoalbum_photos SET lastchanged=$now WHERE id_photo=$id_photo");
	
	$sql = mysql_query("SELECT id_tag, id_photo, x, y, width, height FROM photoalbum_tags WHERE id_photo=$id_photo");
	while($row=mysql_fetch_assoc($sql))
	{
		$width = $row['height'];
		$height = $row['width'];
		$x = $imagesize[1] - $row['y'] - $row['height'];
		$y = $row['x'];
		mysql_query("UPDATE photoalbum_tags SET x=$x, y=$y, width=$width, height=$height WHERE id_tag=$row[id_tag]");		
	}
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewphoto.php?id_photo='.$id_photo);
}
else
	header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/viewphoto.php?id_photo='.$id_photo);

?>
