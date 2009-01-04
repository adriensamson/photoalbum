<?php

include('../common.php');

$mailinvited = true;

$subject='L\'album photo a fait peau neuve';
$message="Bonjour à tous,
J'ai le plaisir de vous annoncer que l'album photo ressemble enfin à quelque chose...
Et en plus il est enfin compatible avec Internet Explorer :-)

Adrien
";

$header="Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$where = (!$mailinvited) ? " WHERE invite IS NULL" : "";
$sql = mysql_query("SELECT invite, email FROM photoalbum_users".$where);
while($row=mysql_fetch_assoc($sql))
{
	if ($row['invite'])
		$msg = $message."\n\n--\n\nVous avez été invité mais n'êtes jamais venu.\nPour vous enregistrer, cliquez ici :\nhttp://www.kyklydse.com/photoalbum/invite.php?action=invited&invite=".$row['invite'];
	else
		$msg = $message;
	mail($row['email'], $subject, $msg, $header, '-f <photoalbum@kyklydse.com>');
}
?>
