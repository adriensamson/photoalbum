<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers">
	<link href="styles/viewuser.css" rel="stylesheet" type="text/css"/>
	<script src="styles/viewuser.js" type="text/javascript"/>
</xsl:template>

<xsl:template match="body[@page='viewuser']">
	<xsl:apply-templates select="album"/>
</xsl:template>

<xsl:template match="album">
	<div class="album">
		<div class="albumheader">Dans <a href="viewalbum.php?id_album={id}"><xsl:value-of select="title"/></a>
		<span class="author"> de <xsl:value-of select="author"/></span>.</div>
		<xsl:apply-templates select="photo"/>
	</div>
</xsl:template>

<xsl:template match="photo">
	<div class="photo">
		<a href="viewphoto.php?id_photo={id}&amp;id_user={/photoalbum/iduser}">
			<img src="photo_thumb_{id}.jpg" onmouseover="showlegend({id})" onmouseout="hidelegend({id})"/>
		</a>
		<div class="legend" id="legend{id}" onmouseover="showlegend({id})" onmouseout="hidelegend({id})">
			<span class="nbcomments"><xsl:apply-templates select="nbcomments"/></span>
			<xsl:apply-templates select="peoples"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="peoples">
	<xsl:if test="people">
		<br/><span class="people">Avec : <xsl:apply-templates select="people"/></span>
	</xsl:if>
</xsl:template>

<xsl:template match="people[position()!=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>,
</xsl:template>
<xsl:template match="people[position()=last()]">
	<a href="viewuser.php?id_user={id}"><xsl:value-of select="name"/></a>.
</xsl:template>

<xsl:template match="nbcomments">
	<xsl:choose>
		<xsl:when test=". = 0">
			<xsl:text>Pas de commentaire.</xsl:text>
		</xsl:when>
		<xsl:when test=". = 1">
			<xsl:text>1 commentaire.</xsl:text>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="."/><xsl:text> commentaires</xsl:text>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>