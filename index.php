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
header('Content-Type: application/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/index.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<title>Accueil</title>
	<menuitem>
		<title>Accueil</title>
		<link>index.php</link>
	</menuitem>
	<body page='index'>";

$can_access=select_can_access_album($id_user);
$sql=mysql_query("SELECT title, id_album, id_owner FROM photoalbum_albums WHERE id_album IN
		($can_access) ORDER BY title ASC");
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
	echo "<album>
			<id>$id_album</id>
			<name>$title</name>
			<author>$author</author>
			<nbphotos>$nbphotos</nbphotos>";
	if (isset($unseen[$id_album]))
		echo "<changed/>";
	echo "<peoples>";
	while ($row2=mysql_fetch_assoc($sql2))
		echo "<people><id>$row2[id_user]</id><name>$row2[name]</name></people>";
	echo "</peoples></album>";
}

echo "</body></photoalbum>";

?>
