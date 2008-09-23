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
	header('Content-Type: application/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/invite.xsl' type='text/xsl'?>
<photoalbum>
	<login>$user[name]</login>
	<title>Invitation</title>";
	if (isset($_SERVER['HTTP_REFERER']))
		echo "<redirect>$_SERVER[HTTP_REFERER]</redirect>";
	echo "<body page='invite'/></photoalbum>";
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
		header('Content-Type: application/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>
<?xml-stylesheet href='styles/invite.xsl' type='text/xsl'?>
<photoalbum>
	<title>Invité</title>
	<body page='invited'>
		<invite>$invite</invite>
	</body>
</photoalbum>";
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
