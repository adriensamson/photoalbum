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
		<ul><xsl:for-each select="tag">
			<li><xsl:value-of select="name"/> - <a href="editphoto.php?action=deletetag&amp;id_photo={/photoalbum/idphoto}&amp;id_tag={idtag}">
				<img src="icons/edit-delete.png" alt="Supprimer" title="Supprimer"/></a></li>
		</xsl:for-each></ul>
	</p>
</xsl:template>

<xsl:template match="body[@page='deletephoto']">
	<p>
		Êtes-vous sûr de vouloir supprimer l'album <xsl:value-of select="title"/> ?<br/>
		<a href="editphoto.php?action=confdelete&amp;id_photo={/photoalbum/idphoto}">Oui</a><br/>
		<a href="viewphoto.php?id_photo={/photoalbum/idphoto}">Non</a>
	</p>
</xsl:template>

</xsl:stylesheet>

