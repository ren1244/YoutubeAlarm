var DataList=[];

var listAlarm;

var AutoEnable=false;
var test;
function initAutoStatus()
{
	AutoEnable=true;
}

function init()
{		
	listAlarm=new alarmList("alarm","imgs/ic_delete_forever_black_24px.svg","svg_clock");
	initYTDiv();
	initMusicDiv();
	initAutoStatus();
	
	var mus=document .getElementById("music").parentNode;
	mus.addEventListener('drop',function (e)
	{
		e.preventDefault();
		var f=e.dataTransfer;
		var myTitle=f.files[0].name;
		getUrlFromFile(f.files[0],function(myURL){
			listMusic.appendMedia({title:myTitle,url:myURL});
		});
		mus .removeAttribute("style");
	});
	mus.addEventListener('dragover',function (e)
	{
		e.preventDefault();
		mus .setAttribute("style","border:1px solid black;");
	});
	mus.addEventListener('dragleave',function (e)
	{
		mus .removeAttribute("style");
	});
	
	
	refreshRecent();
	doResize();
}
function doResize()
{
	var cc=document .getElementsByClassName("container");
	var w=cc[0].parentNode.clientWidth
		-cc[0].offsetWidth
		-cc[2].offsetWidth
		-cc[3].offsetWidth-570;
	cc[1] .setAttribute("style","width:"+(500+(w>0?w:0))+"px");
	var dd=document .getElementById("YTinput").children;
	dd[0].setAttribute("style","width:"+(cc[1].clientWidth-dd[1].offsetWidth-200)+"px");
}

//設定時間
function appAlarm(cid)
{
	var T=prompt("輸入時間(時:分)","");
	var d=new Date;
	var d2=new Date;
	var T=T.split(':');
	if(T.length!=2)
		return;
	T[0]=parseInt(T[0],10);
	T[1]=parseInt(T[1],10);
	if(T[0]<0 || T[0]>23 || T[1]<0 || T[1]>59)
		return;
	d.setMilliseconds(0);
	d.setSeconds(0);
	d.setMinutes(T[1]);
	d.setHours(T[0]);
	if(d.getTime()<=d2.getTime())
		d.setDate(d.getDate()+1);
	if(cid.slice(0,3)=="you")
	{
		var tit=YT.get(cid).getVideoData().title;
		listAlarm.appendAlarm(d,tit,YTplayById(cid));
	}
	if(cid.slice(0,3)=="mus")
	{
		var tit=document .getElementById(cid).children[0].innerHTML;
		listAlarm.appendAlarm(d,tit,MusplayById(cid));
	}
}
function loadFromYT(me)
{
	var inp=document .getElementById("YTinput").children[0];
	var txt=inp.value;
	inp.value="";
	var k=txt.indexOf('v=');
	if(k>=0)
		txt=txt.slice(k+2);
	k=txt.indexOf('&');
	if(k>=0)
		txt=txt.slice(k);
	var res=txt.match(/[0-9a-zA-Z-_]+/g);
	if(res && res.length==1 && res[0].length==11)
		listYoutube.appendMedia(txt);
	else
		inp.value="網址錯誤，重新輸入 youtube 網址...";
}
