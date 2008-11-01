<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='editalbum']">
	<form method="post" action="editalbum.php">
		<p>
			<input type="hidden" name="action" value="edit"/>
			<input type="hidden" name="id_album" value="{/photoalbum/idalbum}"/>
			Titre de l'album : <input name="title" value="{title}"/><br/>
			Date de l'album : <input name="date_album" value="{date}"/><br/>
			<input type="submit" value="Modifier"/>
		</p>
	</form>
</xsl:template>

<xsl:template match="body[@page='deletealbum']">
	<p>
		Êtes-vous sûr de vouloir supprimer l'album <xsl:value-of select="title"/> ?<br/>
		<a href="editalbum.php?action=confdelete&amp;id_album={/photoalbum/idalbum}">Oui</a><br/>
		<a href="viewalbum.php?id_album={/photoalbum/idalbum}">Non</a>
	</p>
</xsl:template>

</xsl:stylesheet>

