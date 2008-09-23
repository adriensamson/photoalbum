function affcadre(num)
{
	var i=0;
	while (document.getElementById('cadre'+i))
	{	
		document.getElementById('cadre'+i).style.visibility='hidden';
		i++;
	}
	if (num != -1) document.getElementById('cadre'+num).style.visibility='visible';
}
