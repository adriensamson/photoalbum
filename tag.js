var clic = 0;
var x1 = 0;
var x2 = 0;
var y1 = 0;
var y2 = 0;
var resized = false;
var ratio = Math.min(800/imgw, 1);
function resize()
{
	if (!resized)
	{
		ratio = Math.min(800/imgw, 1)
		document.getElementById('photo').width=800;
		resized=true;
	}
	else
	{
		ratio = 1;
		document.getElementById('photo').width=imgw;
		resized=false;
	}
	if (clic > 1)
	{
		document.getElementById('rect').style.left=document.getElementById('photo').offsetLeft + x1*ratio + 'px';
		document.getElementById('rect').style.top=document.getElementById('photo').offsetTop + y1*ratio + 'px';
		document.getElementById('rect').style.width=(x2-x1)*ratio + 'px';
		document.getElementById('rect').style.height=(y2-y1)*ratio + 'px';
	}
}
function clicked(ev)
{
	var x = (ev.pageX - document.getElementById('photo').offsetLeft)/ratio;
	var y = (ev.pageY - document.getElementById('photo').offsetTop)/ratio;
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
		if (Math.abs(x1-x) < Math.abs(x2-x))
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
		document.getElementById('rect').style.position='absolute';
		document.getElementById('rect').style.border='solid 2px red';
		document.getElementById('rect').style.left=document.getElementById('photo').offsetLeft + x1*ratio + 'px';
		document.getElementById('rect').style.top=document.getElementById('photo').offsetTop + y1*ratio + 'px';
		document.getElementById('rect').style.width=(x2-x1)*ratio + 'px';
		document.getElementById('rect').style.height=(y2-y1)*ratio + 'px';
		document.getElementById('rect').style.visibility='visible';
	}
}