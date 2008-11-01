function more(id)
{
	document.getElementById('more'+id).style.display = 'none';
	document.getElementById('less'+id).style.display = 'inline';
	document.getElementById('thumb'+id).style.display = 'inline';
	document.getElementById('peoples'+id).style.display = 'inline';
}

function less(id)
{
	document.getElementById('more'+id).style.display = 'inline';
	document.getElementById('less'+id).style.display = 'none';
	document.getElementById('thumb'+id).style.display = 'none';
	document.getElementById('peoples'+id).style.display = 'none';
}