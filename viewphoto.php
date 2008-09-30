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

header('Content-Type: application/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/viewphoto.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<title>$albumtitle - Photo</title>
	<idphoto>$id_photo</idphoto>";
if (is_owner($user['id_user'], $id_album))
	echo "<owner/>";
echo "<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<menuitem>
		<title>$albumtitle</title>
		<link>viewalbum.php?id_album=$id_album</link>
	</menuitem>
	<menuitem>
		<title>Taguer</title>
		<link>tag.php?id_photo=$id_photo</link>
	</menuitem>";
if (isset($id_user))
{
	echo "<menuitem>
	<title>$id_user_name</title>
	<link>viewuser.php?id_user=$id_user</link>
	</menuitem>";
	$select_can_access = select_can_access_photo($user['id_user']);
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos
		WHERE ((id_album = $id_album AND id_photo > $id_photo) OR id_album > $id_album)
		AND id_photo IN ($select_can_access)
		AND id_photo IN (SELECT id_photo FROM photoalbum_tags WHERE id_user = $id_user)
		ORDER BY id_album ASC, id_photo ASC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
		echo "<next>viewphoto.php?id_photo=$row[id_photo]&amp;id_user=$id_user</next>";
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos
		WHERE ((id_album = $id_album AND id_photo < $id_photo) OR id_album < $id_album)
		AND id_photo IN ($select_can_access)
		AND id_photo IN (SELECT id_photo FROM photoalbum_tags WHERE id_user = $id_user)
		ORDER BY id_album DESC, id_photo DESC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
		echo "<prev>viewphoto.php?id_photo=$row[id_photo]&amp;id_user=$id_user</prev>";
}
else
{
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album = $id_album AND id_photo > $id_photo ORDER BY id_photo ASC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
		echo "<next>viewphoto.php?id_photo=$row[id_photo]</next>";
	$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album = $id_album AND id_photo < $id_photo ORDER BY id_photo DESC LIMIT 1");
	if($row = mysql_fetch_assoc($sql))
		echo "<prev>viewphoto.php?id_photo=$row[id_photo]</prev>";
}
echo "<body page='viewphoto'>";

$sql = mysql_query("SELECT u.id_user, u.name, t.x, t.y, t.height, t.width FROM photoalbum_tags AS t LEFT JOIN photoalbum_users AS u ON (t.id_user=u.id_user) WHERE t.id_photo = $id_photo ORDER BY u.name ASC");
while ($row=mysql_fetch_assoc($sql))
{
	echo "<cadre x='$row[x]' y='$row[y]' h='$row[height]' w='$row[width]'>
			<people>
				<id>$row[id_user]</id>
				<name>$row[name]</name>
			</people>
		</cadre>";
}

$sql=mysql_query("SELECT u.id_user, u.name, c.comment FROM photoalbum_comments AS c LEFT JOIN photoalbum_users AS u ON (u.id_user = c.id_user) WHERE c.id_photo=$id_photo ORDER BY c.id_comment ASC");
while ($row=mysql_fetch_assoc($sql))
{
	echo "<comment>
			<people>
				<id>$row[id_user]</id>
				<name>$row[name]</name>
			</people>
			<text>$row[comment]</text>
		</comment>";
}
echo "</body></photoalbum>";

?>
