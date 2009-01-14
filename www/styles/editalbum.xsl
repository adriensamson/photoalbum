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
			Date de l'album : <input name="album_date" value="{date}"/><br/>
			<input type="submit" value="Modifier"/>
		</p>
	</form>
	<xsl:apply-templates select="guests"/>
</xsl:template>

<xsl:template match="guests">
	<xsl:if test="guest">
	<p>Liste des invités de l'album :<br/>
	<ul><xsl:for-each select="guest">
		<li>
			<a href="editalbum.php?action=deleteguest&amp;id_album={/photoalbum/idalbum}&amp;id_guest={idguest}">
				<img src="icons/edit-delete.png" alt="Supprimer" title="Supprimer"/>
			</a>
			<xsl:value-of select="name"/>
		</li>
	</xsl:for-each></ul>
	</p></xsl:if>
	<form method="post" action="editalbum.php">
		<p>
			<input type="hidden" name="action" value="addguest"/>
			<input type="hidden" name="id_album" value="{/photoalbum/idalbum}"/>
			<select name="id_guest">
				<xsl:for-each select="people">
					<option value="{id}"><xsl:value-of select="name"/></option>
				</xsl:for-each>
			</select>
			<input type="submit" value="Inviter"/>
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

