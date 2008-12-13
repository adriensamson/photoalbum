<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='login']">
	<form method="post" action="login.php">
		<p>
			<input type="hidden" name="action" value="login"/>
			E-mail : <input name="email"/><br/>
			Mot de passe : <input type="password" name="passwd"/><br/>
			<input type="submit" value="S'identifier"/>
		</p>
	</form>
	<p><a href="login.php?action=lostpasswd">Mot de passe oublié ?</a></p>
</xsl:template>

<xsl:template match="body[@page='lostpasswd']">
	<form method="post" action="login.php">
		<p>
			<input type="hidden" name="sendinvite" value="login"/>
			E-mail : <input name="email"/><br/>
			<input type="submit" value="Renvoyer une invitation"/>
		</p>
	</form>
</xsl:template>

</xsl:stylesheet>




