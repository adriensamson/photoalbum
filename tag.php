<?php
/*
 * Created on 19 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include("common.php");
$user = auth();
if ($user['id_user']==-1)
{
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}
if (!isset($_REQUEST['action']))
{
	$id_photo=intval($_REQUEST['id_photo']);
	$sql=mysql_query("SELECT a.id_album, a.title FROM photoalbum_photos AS p LEFT JOIN photoalbum_albums AS a ON (a.id_album=p.id_album) WHERE p.id_photo=$id_photo");
	$row=mysql_fetch_assoc($sql);
	$id_album=$row['id_album'];
	$albumtitle=$row['title'];
	$sql = mysql_query("SELECT name, id_user FROM photoalbum_users ORDER BY name ASC");
	$options = array();
	while ($row=mysql_fetch_assoc($sql))
		$options[$row['id_user']]=$row['name'];
	header('Content-Type: application/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/tag.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$albumtitle - Tag</title>
	<idphoto>$id_photo</idphoto>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$albumtitle</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<menuitem>
		<title>Photo</title>
		<link>viewphoto.php?id_photo=$id_photo</link>
	</menuitem>
	<menuitem>
		<title>Inviter quelqu'un</title>
		<link>invite.php</link>
	</menuitem>
	<body page='tag'><peoplelist>";

	foreach($options as $id_user => $name)
		echo "<people><id>$id_user</id><name>$name</name></people>";

	echo "</peoplelist></body></photoalbum>";
}
elseif ($_REQUEST['action']=='tag')
{
	$id_photo=intval($_POST['id_photo']);
	$username=mysql_real_escape_string($_POST['name']);
	$id_user=intval($_REQUEST['id_user']);
	if (!can_access_photo($user['id_user'], $id_photo))
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	$id_tager=intval($user['id_user']);
	$x=intval($_POST['x']);
	$y=intval($_POST['y']);
	$height=intval($_POST['height']);
	$width=intval($_POST['width']);
	
	$sql = mysql_query("SELECT id_tag FROM photoalbum_tags WHERE id_user=$id_user AND id_photo=$id_photo");
	if (mysql_num_rows($sql)==0)
	{
		if ($x != -1) mysql_query("INSERT INTO photoalbum_tags (id_user, id_photo, x, y, height, width, id_tager) VALUES ($id_user, $id_photo, $x, $y, $height, $width, $id_tager)");
	}
	else
	{
		$result = mysql_fetch_assoc($sql);
		$id_tag = $result['id_tag'];
		if ($x != -1)
			mysql_query("UPDATE photoalbum_tags SET x=$x, y=$y, height=$height, width=$width, id_tager=$id_tager WHERE id_tag=$id_tag");
		else
			mysql_query("DELETE FROM photoalbum_tags WHERE id_tag=$id_tag");
	}
	
	// est ce le premier tag de cette personne dans l'album ?
	$sql = mysql_query("SELECT id_tag FROM photoalbum_tags WHERE id_photo IN (SELECT id_photo FROM photoalbum_photos WHERE id_album IN (SELECT id_album FROM photoalbum_photos WHERE id_photo=$id_photo))");
	if(mysql_num_rows($sql)==1)
	{
		// on marque tout l'album comme non vu
		$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album IN (SELECT id_album FROM photoalbum_photos WHERE id_photo=$id_photo)");
		while($row=mysql_fetch_assoc($sql))
			set_unseen($id_user, $row['id_photo']);
	}
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/viewphoto.php?id_photo=$id_photo";
	header("Location: $url");
}
?>
