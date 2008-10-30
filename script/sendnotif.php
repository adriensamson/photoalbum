<?php

include('../common.php');

$subject='Il y a du nouveau sur photoalbum';
$message="Bonjour NAME,
Je vous annonce qu'il y a du nouveau sur photoalbum depuis votre dernière visite.\n
Je vous propose donc de venir voir : http://www.kyklydse.com/photoalbum\n

Et n'oubliez pas que vous pouvez tout marquer comme vu en cliquant sur les petites lunettes ;)
Photoalbum";

$header='Content-Type: text/plain; charset="UTF-8"\r\n';
$maxvisit = time() - 600; // dernière visite il y a plus de 10min
$sql = mysql_query("SELECT email, name FROM photoalbum_users WHERE invite IS NULL AND lastvisit > lastmail AND lastvisit < $maxvisit AND id_user IN (SELECT id_user FROM photoalbum_unseen_changes)");
while($row=mysql_fetch_assoc($sql))
{
	mail($row['email'], $subject, str_replace("NAME", $row['name'], $msg), $header, '-f photoalbum@kyklydse.com');
}
?>
