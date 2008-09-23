var clic = 0;
var x1 = 0;
var x2 = 0;
var y1 = 0;
var y2 = 0;

function clicked(ev)
{
	var x = ev.layerX;
	var y = ev.layerY;
	if (clic == 0)
	{
		x1=x;
		y1=y;
		clic=1;
	}
	else if (clic == 1)
	{
		x2=x;
		y2=y;
		clic=2;
	}
	else
	{
		if (Math.abs(x1-x) + Math.abs(y1-x) < Math.abs(x2-x) + Math.abs (y2-y))
		{
			x1=x;
			y1=y;
		}
		else
		{
			x2=x;
			y2=y;
		}
	}
	document.getElementById('x').value=x1;
	document.getElementById('y').value=y1;
	document.getElementById('width').value=Math.max(0, x2-x1);
	document.getElementById('height').value=Math.max(0, y2-y1);
	if (clic > 1)
	{
		document.getElementById('rect').style.left= x1 + 'px';
		document.getElementById('rect').style.top= y1 + 'px';
		document.getElementById('rect').style.width=(x2-x1) + 'px';
		document.getElementById('rect').style.height=(y2-y1) + 'px';
		document.getElementById('rect').style.visibility='visible';
	}
}