//v.3.6 build 130417

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function dhx_init_sliders(){for(var f=document.getElementsByTagName("input"),d=0;d<f.length;d++)if(f[d].className=="dhtmlxSlider"){var a=f[d],g=a.getAttribute("position")||"over",b=document.createElement("DIV");b.style.width=a.offsetWidth+"px";b.style.height=a.offsetHeight+"px";a.parentNode.insertBefore(b,a);if(g=="over")a.style.display="none";else{var c=document.createElement("DIV"),e=Math.round(a.offsetWidth/3);e>50&&(e=50);c.style.width=a.offsetWidth-e+"px";b.style.position="relative";c.style[g==
"left"?"right":"left"]=c.style.top=a.style.top=a.style[g]="0px";c.style.position=a.style.position="absolute";a.style.width=e+"px";c.style.height=a.offsetHeight+"px";b.appendChild(a);b.appendChild(c);b=c}var h=new dhtmlxSlider(b,b.offsetWidth,a.getAttribute("skin")||"",!1,a.getAttribute("min")||"",a.getAttribute("max")||"",a.value,a.getAttribute("step")||"");h.linkTo(a);h.init()}}
window.addEventListener?window.addEventListener("load",dhx_init_sliders,!1):window.attachEvent&&window.attachEvent("onload",dhx_init_sliders);

//v.3.6 build 130417

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/