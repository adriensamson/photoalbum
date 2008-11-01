<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers">
	<link href="styles/tag.css" rel="stylesheet" type="text/css"/>
	<script src="styles/tag.js" type="text/javascript"></script>
</xsl:template>

<xsl:template match="body[@page='tag']">
	<div class="container">
		<img src="photo_{/photoalbum/idphoto}.jpg" id="photo" alt="photo" onclick="clicked(event)"/>
		<div id="rect" onclick="clickedrect(event)"/>
	</div>
	<form method="post" action="tag.php">
		<p>
			<select name="id_user"><xsl:apply-templates select="peoplelist"/></select>
			<input name="fake_tag"/>
			<input id="x" type="hidden" name="x"/>
			<input id="y" type="hidden" name="y"/>
			<input id="height" type="hidden" name="height"/>
			<input id="width" type="hidden" name="width"/>
			<input type="hidden" name="id_photo" value="{/photoalbum/idphoto}"/>
			<input type="hidden" name="action" value="tag"/>
			<input type="submit" value="Taguer"/>
		</p>
	</form>
</xsl:template>

<xsl:template match="peoplelist">
	<option value="-1">Faux tag...</option>
	<xsl:for-each select="people">
		<option value="{id}"><xsl:value-of select="name"/></option>
	</xsl:for-each>
</xsl:template>
</xsl:stylesheet>
