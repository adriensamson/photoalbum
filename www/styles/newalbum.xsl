<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='newalbum']">
	<form method="post" action="newalbum.php">
		<p>
			Nom de l'album : <input name="title"/><br/>
			Date de l'album : <input name="album_date" value="{date}"></input><br/>
			<input type="submit" value="Créer"/>
			<input type="hidden" name="action" value="addalbum"/>
		</p>
	</form>
</xsl:template>

</xsl:stylesheet>
