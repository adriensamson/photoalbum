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
			Nom : <input name="username"/><br/>
			Mot de passe : <input type="password" name="passwd"/><br/>
			<input type="submit" value="S'identifier"/>
		</p>
	</form>
</xsl:template>

</xsl:stylesheet>




