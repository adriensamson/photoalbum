<?php
/*
 * Created on 25 juin 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include('common.php');
$user=auth();

if(isset($_REQUEST['action']) && $_REQUEST['action']=='logout')
{
	setcookie('photoalbum','');
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}
elseif ($user['id_user']!=-1)
{
	$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
	header("Location: $url");
}
elseif (!isset($_REQUEST['action']))
{
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		header('Content-Type: text/html');
	else
		header('Content-Type: application/xhtml+xml');
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>Identification</title></head>
<body>
<form method='post' action='login.php'>
<p>
<input type='hidden' name='action' value='login'/>
Nom : <input name='username'/><br/>
Mot de passe : <input type='password' name='passwd'/><br/>
<input type='submit' value='Login'/>
</p>
</form>
</body></html>";
}
else
{
	$username = mysql_real_escape_string($_REQUEST['username']);
	$md5 = md5($_REQUEST['passwd']);
	$sql = mysql_query("SELECT id_user, md5 FROM photoalbum_users WHERE name='$username' AND md5='$md5'");
	if (mysql_num_rows($sql)==0)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		header("Location: $url");
	}
	else
	{
		$row = mysql_fetch_assoc($sql);
		setcookie('photoalbum', serialize($row),time()+3600*24*365);
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
		header("Location: $url");
	}
}
?>
