<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='newphoto']">
	<xsl:call-template name="lastphoto"/>
	<form method="post" action="newphoto.php" enctype="multipart/form-data">
		<p>
			<input name="photo" type="file"/><br/>
			<input type="hidden" name="id_album" value="{/photoalbum/idalbum}"/>
			<input type="hidden" name="action" value="upload"/>
			<input type="submit" value="Ajouter"/>
		</p>
	</form>
</xsl:template>

<xsl:template name="lastphoto">
	<xsl:if test="/photoalbum/lastphoto">
		<p>Dernière photo envoyée :<br />
			<a href="viewphoto.php?id_photo={/photoalbum/lastphoto/id}">
				<img src="photo.php?thumb=y&amp;id_photo={/photoalbum/lastphoto/id}" alt="{/photoalbum/lastphoto/filename}"/>
			</a>
		</p>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
