<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers">
	<link href="styles/index.css" rel="stylesheet" type="text/css"/>
</xsl:template>

<xsl:template match="body[@page='index']">
	<div class="create">
		<a href="newalbum.php">
			<img src="icons/folder-new.png" alt="Nouvel album" title="Nouvel album"/>
		</a>
		<a href="index.php?markseen=y">
			<img src="icons/mark-read.png" alt="Tout marquer comme vu" title="Tout marquer comme vu"/>
		</a>
	</div>
	<xsl:apply-templates select="album"/>
</xsl:template>

<xsl:template match="album">
	<div class="album">
		<xsl:if test="changed">
			<xsl:attribute name="class">album changed</xsl:attribute>
		</xsl:if>
		<a href="viewalbum.php?id_album={id}"><img src="photo_album_{id}.jpg"/><strong><xsl:value-of select="name"/></strong></a>
		<span class="author"> de <xsl:value-of select="author"/></span>,
		<span class="nbphotos"><xsl:value-of select="nbphotos"/> photos.</span><br/>
		<span class="people">Avec : <xsl:apply-templates select="peoples/people"/></span>
		<hr class="spacer"/>
	</div>
</xsl:template>

<xsl:template match="people[position()!=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>,
</xsl:template>
<xsl:template match="people[position()=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>.
</xsl:template>

</xsl:stylesheet>