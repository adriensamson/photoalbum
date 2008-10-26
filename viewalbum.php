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
	set_all_seen($id_user);
$unseen = get_unseen($user['id_user']);
$sql = mysql_query("SELECT title FROM photoalbum_albums WHERE id_album=$id_album");
$row = mysql_fetch_assoc($sql);
$title = $row['title'];
$sql = mysql_query("SELECT id_photo FROM photoalbum_photos WHERE id_album=$id_album ORDER BY id_photo ASC");

header('Content-Type: application/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/viewalbum.xsl' type='text/xsl'?>
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
	<menuitem>
		<title>Archive ZIP</title>
		<link>album_$id_album.zip</link>
	</menuitem>";

if (is_owner($user['id_user'], $id_album))
	echo "<owner/>";

echo "<body page='viewalbum'>";

while ($row = mysql_fetch_assoc($sql))
{
	$id_photo=$row['id_photo'];
	$sql2 = mysql_query("SELECT COUNT(*) FROM photoalbum_comments WHERE id_photo=$id_photo");
	$row2 = mysql_fetch_row($sql2);
	$nbcomments = $row2[0];
	$whois = select_whois_in_photo($id_photo);
	$sql2 = mysql_query("SELECT id_user, name FROM photoalbum_users WHERE id_user IN ($whois) ORDER BY name ASC");
	echo "<photo>
		<id>$id_photo</id>
		<nbcomments>$nbcomments</nbcomments>";
	if (isset($unseen[$id_photo]))
		echo "<changed/>";	
	echo "<peoples>";
	while ($row2=mysql_fetch_assoc($sql2))
		echo "<people><id>$row2[id_user]</id><name>$row2[name]</name></people>";
	echo "</peoples></photo>";
}
echo "</body></photoalbum>";

?>
