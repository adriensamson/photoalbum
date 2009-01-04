<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml"
  doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" 
  doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes"/>
<xsl:include href="global.xsl"/>

<xsl:template name="headers">
	<link href="styles/viewphoto.css" rel="stylesheet" type="text/css"/>
	<script src="styles/viewphoto.js" type="text/javascript"></script>
	<style type="text/css">
		<xsl:call-template name="cadrestyle"/>
	</style>
</xsl:template>

<xsl:template match="body[@page='viewphoto']">
	<xsl:call-template name="editphoto"/>
	<div class="container">
		<img src="photo_{/photoalbum/idphoto}.jpg" id="photo" alt="photo"/><br/>
		<div class="photolegend"><xsl:value-of select="legend"/></div>
		<xsl:apply-templates select="cadre"/>
	</div>
	<xsl:call-template name="peoplelist"/>
	<xsl:call-template name="comments"/>
</xsl:template>

<xsl:template match="cadre">
	<div class="cadrecont" id="cadrecont{position()}" onmouseover="affcadre({position()})" onmouseout="affcadre(-{position()})">
		<div class="cadre" id="cadre{position()}">
			<span class="legend"><xsl:value-of select="people/name"/></span>
		</div>
	</div>
</xsl:template>

<xsl:template name="cadrestyle">
	<xsl:for-each select="/photoalbum/body/cadre">
#cadrecont<xsl:value-of select="position()"/>
{
left: <xsl:value-of select="@x"/>px;
top: <xsl:value-of select="@y"/>px;
width: <xsl:value-of select="@w"/>px;
height: <xsl:value-of select="@h"/>px;
}
	</xsl:for-each>
</xsl:template>

<xsl:template name="peoplelist">
	<xsl:if test="/photoalbum/body/cadre">
		<p>Sur cette photo : 
		<xsl:for-each select="/photoalbum/body/cadre">
			<xsl:choose>
				<xsl:when test="people/id">
					<a href="viewuser.php?id_user={people/id}" onmouseover="affcadre(0); affcadre({position()})" onmouseout="affcadre(0)"><xsl:value-of select="people/name"/></a>
				</xsl:when>
				<xsl:otherwise>
					<span onmouseover="affcadre(0); affcadre({position()})" onmouseout="affcadre(0)"><xsl:value-of select="people/name"/></span>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="position()!=last()">, </xsl:if>
		</xsl:for-each>.
		</p>
	</xsl:if>
</xsl:template>

<xsl:template name="comments">
	<xsl:if test="/photoalbum/body/comment">
		<div class="commentbox">Commentaires :
			<xsl:for-each select="/photoalbum/body/comment">
				<div class="comment">
					<span class="writer"><a href="viewuser.php?id_user={people/id}"><xsl:value-of select="people/name"/></a> a écrit : </span>
					<xsl:value-of select="text"/>
				</div>
			</xsl:for-each>
		</div>
	</xsl:if>
	<form method="post" action="comment.php">
		<div class="commentinput">
			Ajouter un commentaire :<br/>
			<textarea cols="80" rows="5" name="comment"></textarea><br/>
			<input type="hidden" name="id_photo" value="{/photoalbum/idphoto}"/>
			<input type="submit" value="Envoyer"/>
		</div>
	</form>
</xsl:template>

<xsl:template name="editphoto">
	<xsl:if test="/photoalbum/owner">
		<div class="editphoto">
			<a href="editphoto.php?id_photo={/photoalbum/idphoto}">
				<img src="icons/gtk-edit.png" alt="Modifier" title="Modifier"/>
			</a>
			<a href="editphoto.php?action=rotateleft&amp;id_photo={/photoalbum/idphoto}">
				<img src="icons/object-rotate-left.png" alt="Tourner vers la gauche" title="Tourner vers la gauche"/>
			</a>
			<a href="editphoto.php?action=rotateright&amp;id_photo={/photoalbum/idphoto}">
				<img src="icons/object-rotate-right.png" alt="Tourner vers la droite" title="Tourner vers la droite"/>
			</a>
			<a href="editphoto.php?action=delete&amp;id_photo={/photoalbum/idphoto}">
				<img src="icons/edit-delete.png" alt="Supprimer" title="Supprimer"/>
			</a>
		</div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
