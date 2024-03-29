<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers"/>

<xsl:template match="body[@page='editphoto']">
	<p>
		Liste des tags de cette photo :<br/>
		<xsl:if test="tag">
		<ul><xsl:for-each select="tag">
			<li>
				<a href="editphoto.php?action=deletetag&amp;id_photo={/photoalbum/idphoto}&amp;id_tag={idtag}">
					<img src="icons/edit-delete.png" alt="Supprimer" title="Supprimer"/>
				</a>
				<xsl:value-of select="name"/>
			</li>
		</xsl:for-each></ul></xsl:if>
	</p>
	<form method="post" action="editphoto.php">
		<p>
			Légende :
			<input name="legend" value="{legend}" size="60"/>
			<input type="hidden" name="action" value="editlegend"/>
			<input type="hidden" name="id_photo" value="{/photoalbum/idphoto}"/>
			<input type="submit" value="Modifier"/>
		</p>
	</form>
</xsl:template>

<xsl:template match="body[@page='deletephoto']">
	<p>
		Êtes-vous sûr de vouloir supprimer cette photo ?<br/>
		<a href="editphoto.php?action=confdelete&amp;id_photo={/photoalbum/idphoto}">Oui</a><br/>
		<a href="viewphoto.php?id_photo={/photoalbum/idphoto}">Non</a>
	</p>
</xsl:template>

</xsl:stylesheet>

