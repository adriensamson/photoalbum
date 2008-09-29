function affcadre(num)
{
	if(num==0)
	{
		var i=1;
		while (document.getElementById('cadre'+i))
		{	
			document.getElementById('cadre'+i).style.visibility='hidden';
			i++;
		
		}
	}
	else if (num < 0)
		document.getElementById('cadre'+(-num)).style.visibility='hidden';
	else
		document.getElementById('cadre'+num).style.visibility='visible';
}
