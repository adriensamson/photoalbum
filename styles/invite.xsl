<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='invite']">
	<form method="post" action="invite.php">
		<p>
			Nom : <input name="name"/><br/>
			E-mail : <input name="email"/><br/>
			Message personnel :<br/>
			<textarea name="persmsg" cols="80" rows="10"></textarea><br/>
			<input type="hidden" name="action" value="invite"/>
			<xsl:call-template name="redirect"/>
			<input type="submit" value="Inviter"/>
		</p>
	</form>
</xsl:template>

<xsl:template name="redirect">
	<xsl:if test="/photoalbum/redirect">
		<input type="hidden" name="redirect" value="{/photoalbum/redirect}"/>
	</xsl:if>
</xsl:template>

<xsl:template match="body[@page='invited']">
	<form method="post" action="invite.php">
		<p>
			<input type="hidden" name="action" value="setpasswd"/>
			<input type="hidden" name="invite" value="{invite}"/>
			Veuillez choisir un mot de passe : <input type="password" name="passwd"/><br/>
			Vérification : <input type="password" name="passwd2"/><br/>
			<input type="submit" value="Envoyer"/>
		</p>
	</form>
</xsl:template>

</xsl:stylesheet>
