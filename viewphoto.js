var cadre = new Array(n);
var ratio = Math.min(800/imgw, 1);
var resized = false;
var initialized = false;
function resize()
{
	if (initialized)
	{
		if (!resized)
		{
			ratio = Math.min(800/imgw, 1)
			document.getElementById('photo').width=800;
			offx=document.getElementById('photo').offsetLeft;
			offy=document.getElementById('photo').offsetTop;
			resized=true;
		}
		else
		{
			ratio = 1;
			document.getElementById('photo').width=imgw;
			offx=document.getElementById('photo').offsetLeft;
			offy=document.getElementById('photo').offsetTop;
			resized=false;
		}
		for (var i=0; i < n; i++)
		{
			cadre[i].style.left=offx+x[i]*ratio+'px';
			cadre[i].style.top=offy+y[i]*ratio+'px';
			cadre[i].style.width=w[i]*ratio+'px';
			cadre[i].style.height=h[i]*ratio+'px';
		}
	}
}
function affcadre(num)
{
	if(initialized)
	{
		for (var i=0; i<n; i++)
			cadre[i].style.visibility='hidden';
		if (num < n) cadre[num].style.visibility='visible';
	}
}
function mouse(event)
{
	if(initialized)
	{
		var mx=(event.pageX-document.getElementById('photo').offsetLeft)/ratio;
		var my=(event.pageY-document.getElementById('photo').offsetTop)/ratio;
		for (var i=0; i<n; i++)
		{
			if (x[i] <= mx && mx < x[i]+w[i] && y[i] < my && my < y[i]+h[i])
				cadre[i].style.visibility='visible';
			else
				cadre[i].style.visibility='hidden';
		}
	}
}
function creatediv()
{
	for (var i=0; i<n; i++)
	{
		cadre[i]=document.createElement('div');
		cadre[i].style.border='solid 2px red';
		cadre[i].style.position='absolute';
		cadre[i].style.visibility='hidden';
		cadre[i].onmousemove=mouse;
		cadre[i].ondblclick=resize;
		var span = document.createElement('span');
		span.className='legend';
		span.textContent=text[i];
		cadre[i].appendChild(span);
		document.body.appendChild(cadre[i]);
	}
	initialized=true;
	resize();
}