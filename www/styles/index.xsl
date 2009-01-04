<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers">
	<link href="styles/index.css" rel="stylesheet" type="text/css"/>
	<script src="styles/index.js" type="text/javascript"></script>
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
		<img id="more{id}" class="moreless" src="icons/tree_plus.png" onclick="more({id})"><xsl:if test="position() &lt;= 5"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if></img>
		<img id="less{id}" class="moreless" src="icons/tree_minus.png" onclick="less({id})"><xsl:if test="position() &gt; 5"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if></img>
		<a href="viewalbum.php?id_album={id}">
			<img id="thumb{id}" class="thumb" src="photo_album_{id}.jpg"><xsl:if test="position() &gt; 5"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if></img>
			<strong><xsl:value-of select="name"/></strong>
		</a>
		<span class="author"> de <xsl:value-of select="author"/></span>,
		<xsl:apply-templates select="nbphotos"/><br/>
		<span id="peoples{id}" class="people"><xsl:if test="position() &gt; 5"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>Avec : <xsl:apply-templates select="peoples/people"/></span>
		<hr class="spacer"/>
	</div>
</xsl:template>

<xsl:template match="nbphotos">
	<span class="nbphotos">
		<xsl:choose>
			<xsl:when test=". = 0">pas de photo.</xsl:when>
			<xsl:when test=". = 1">1 photo.</xsl:when>
			<xsl:otherwise><xsl:value-of select="."/> photos.</xsl:otherwise>
		</xsl:choose>
	</span>
</xsl:template>

<xsl:template match="people[position()!=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>,
</xsl:template>
<xsl:template match="people[position()=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>.
</xsl:template>

</xsl:stylesheet>