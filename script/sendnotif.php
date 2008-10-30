<?php

include('../common.php');

$subject='Il y a du nouveau sur photoalbum';
$message="Bonjour NAME,
Je vous annonce qu'il y a du nouveau sur photoalbum depuis votre dernière visite.\n
Je vous propose donc de venir voir : http://www.kyklydse.com/photoalbum\n

Et n'oubliez pas que vous pouvez tout marquer comme vu en cliquant sur les petites lunettes ;)
Photoalbum";

$header='Content-Type: text/plain; charset="UTF-8"\r\n';
$now = time();
$maxvisit = time() - 600; // dernière visite il y a plus de 10min
$sql = mysql_query("SELECT id_user, email, name FROM photoalbum_users WHERE invite IS NULL AND lastvisit > lastmail AND lastvisit < $maxvisit");
while($row=mysql_fetch_assoc($sql))
{
	//on teste si il y a du nouveau
	if (new_changes($row['id_user'])==0)
		continue;
	mail($row['email'], $subject, str_replace("NAME", $row['name'], $message), $header, '-f photoalbum@kyklydse.com');
	mysql_query("UPDATE photoalbum_users SET lastmail=$now WHERE id_user=$row[id_user]");
}
?>
