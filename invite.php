<?php
/*
 * Created on 25 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include('common.php');
$user=auth();

if (!isset($_REQUEST['action']))
{
	if ($user['id_user']==-1)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		header('Content-Type: text/html');
	else
		header('Content-Type: application/xhtml+xml');
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Invitation</title></head>
<body>
<h1>Invitation</h1>
<form method='post' action='invite.php'>
<p>
Nom : <input name='name'/><br/>
E-mail : <input name='email'/><br/>
Message personnel :<br/>
<textarea name='persmsg' cols='80' rows='10'></textarea><br/>
<input type='hidden' name='action' value='invite'/>";
	if (isset($_SERVER['HTTP_REFERER'])) echo "<input type='hidden' name='redirect' value='$_SERVER[HTTP_REFERER]'/>\n";
	echo "<input type='submit' value='Inviter'/>
</p></form></body></html>";
}
elseif ($_REQUEST['action']=='invite')
{
	if ($user['id_user']==-1)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	$name=$_REQUEST['name'];
	$email=$_REQUEST['email'];
	$persmsg=$_REQUEST['persmsg'];
	$from_name=$user['name'];
	$invite = rand();
	$mname = mysql_real_escape_string($name);
	$memail = mysql_real_escape_string($email);
	$sql = mysql_query("SELECT id_user FROM photoalbum_users WHERE email='$memail' OR name='$mname'");
	if (mysql_num_rows($sql)!=0)
		exit("Cet utilisateur existe déjà.");
	mysql_query("INSERT INTO photoalbum_users (email, name, invite) VALUES ('$memail', '$mname', '$invite')");
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/invite.php?action=invited&invite=$invite";
	$message = "$name, $from_name vous a ajouté et invité sur un album photo.
Pour voir les photos sur lesquelles vous êtes, il suffit de vous inscrire grâce au lien suivant :
$url

$persmsg

A bientôt.
--
Il est conseillé d'utiliser Firefox pour que le site fonctionne correctement.
http://www.getfirefox.com
";
	$subject = "$from_name vous a invité sur un album photo";
	$header='Content-Type: text/plain; charset="UTF-8"\r\n';
	mail($email, $subject, $message, $header, '-f photoalbum@kyklydse.com');
	$url = (isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}
elseif ($_REQUEST['action']=='invited')
{
	$invite = intval($_REQUEST['invite']);
	$sql = mysql_query("SELECT id_user FROM photoalbum_users WHERE invite=$invite");
	if(mysql_num_rows($sql)==0)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	else
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
			header('Content-Type: text/html');
		else
			header('Content-Type: application/xhtml+xml');
		echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Bienvenue</title></head>
<body>
<form method='post' action='invite.php'>
<p>
<input type='hidden' name='action' value='setpasswd'/>
<input type='hidden' name='invite' value='$invite'/>
Veuillez choisir un mot de passe : <input type='password' name='passwd'/><br/>
Vérification : <input type='password' name='passwd2'/><br/>
<input type='submit' value='Envoyer'/>
</p>
</form>
</body></html>";
	}
}
elseif ($_REQUEST['action']=='setpasswd')
{
	$invite = intval($_REQUEST['invite']);
	$md5 = md5($_REQUEST['passwd']);
	$md52 = md5($_REQUEST['passwd2']);
	$sql = mysql_query("SELECT id_user FROM photoalbum_users WHERE invite=$invite");
	if(mysql_num_rows($sql)==0 || $md5 != $md52)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
	else
	{
		$row=mysql_fetch_assoc($sql);
		$id_user = $row['id_user'];
		mysql_query("UPDATE photoalbum_users SET invite=NULL, md5='$md5' WHERE id_user=$id_user");
		$user_array=array('id_user' => $id_user, 'md5' => $md5);
		setcookie('photoalbum', serialize($user_array),time()+3600*24*365);
		$url = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
}
?>
