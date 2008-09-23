<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
  
<xsl:template match="/photoalbum">
	<html>
		<head>
			<link href="styles/global.css" rel="stylesheet" type="text/css"/>
			<title>Photoalbum - <xsl:value-of select="title"/></title>
			<xsl:call-template name="headers"/>
		</head>
		<body>
			<div class="header">
				<xsl:apply-templates select="login"/>
				<div class="title"><a href="index.php">Photoalbum</a></div>
				<div class="menu">
					<xsl:apply-templates select="menuitem|prev|next"/>
				</div>
			</div>
		<div class="body">
			<xsl:apply-templates select="body"/>
			<hr class="spacer"/>
		</div>
		</body>
	</html>
</xsl:template>

<xsl:template match="login">
	<xsl:choose>
		<xsl:when test=". = 'Anonymous'">
			<div class="login"><a href="login.php">Se connecter</a></div>
		</xsl:when>
		<xsl:otherwise>
			<div class="login"><xsl:value-of select="."/>, <a href="login.php?action=logout">Se déconnecter</a></div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="menuitem">
	<span class="menuitem"><a href="{link}"><xsl:value-of select="title"/></a></span>
</xsl:template>

<xsl:template match="prev">
	<span class="menuitem prev"><a href="{.}">Précédent</a></span>
</xsl:template>

<xsl:template match="next">
	<span class="menuitem next"><a href="{.}">Suivant</a></span>
</xsl:template>

</xsl:stylesheet>