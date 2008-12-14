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
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<title>Identification</title>
	<body page='login'/></photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'login');
}
elseif($_REQUEST['action']=='lostpasswd')
{
	$xml_str = "<?xml version='1.0' encoding='UTF-8'?>
<photoalbum>
	<title>Identification</title>
	<body page='lostpasswd'/></photoalbum>";
	$xml_doc = new DOMDocument('1.0', 'UTF-8');
	$xml_doc->loadXML($xml_str);
	render($xml_doc, 'login');
}
elseif($_REQUEST['action']=='sendinvite')
{
	$email = $_REQUEST['email'];
	$memail = mysql_real_escape_string($_REQUEST['email']);
	$invite = rand();
	$sql = mysql_query("SELECT id_user FROM photoalbum_users WHERE email='$memail'");
	if (mysql_num_rows($sql)==0)
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?action=lostpasswd';
		header("Location: $url");
	}
	else
	{
		mysql_query("UPDATE photoalbum_users SET invite=$invite WHERE email='$memail'");
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/invite.php?action=invited&invite=$invite";
		$message = "$name, vous semblez avoir oublié votre mot de passe.
Pour le réinitialiser, il suffit de vous ré-inscrire grâce au lien suivant :
$url

A très bientôt.
--
Il est conseillé d'utiliser Firefox pour que le site fonctionne correctement.
http://www.getfirefox.com
";
	$subject = "Oubli de votre mot de passe sur photoalbum";
	$header="Content-Type: text/plain; charset=\"UTF-8\"\r\n";
	mail($email, $subject, $message, $header, '-f <photoalbum@kyklydse.com>');
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header("Location: $url");
	}
}
else
{
	$email = mysql_real_escape_string($_REQUEST['email']);
	$md5 = md5($_REQUEST['passwd']);
	$sql = mysql_query("SELECT id_user, md5 FROM photoalbum_users WHERE email='$email' AND md5='$md5'");
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
