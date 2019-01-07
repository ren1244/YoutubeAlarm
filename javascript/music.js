var listMusic;

function initMusicDiv()
{
	listMusic=new mediaList(
		"music",
		"imgs/ic_alarm_add_black_24px.svg",
		"imgs/ic_delete_forever_black_24px.svg",
		createMus,null,appMusAlarm);
}
function enableMusAddAlarm()
{
	listMusic.fAddAlarm=appMusAlarm;
}
function createMus(cid,info)
{
	var container=document .getElementById(cid);
	createDomTree(container,[
		['div',null,'class','MusicTitle'],
		['audio',null,'controls',true,'src',info.url]
	]);
	container.children[0].innerHTML=info.title;
	container.children[1].loop=true;
	container.info_title=info.title;
}
function appMusAlarm(cid)
{
	/*if(!AutoEnable)
	{
		alert("無法設定鬧鐘，您的瀏覽器可能禁用了自動撥放！");
		return;
	}*/
	var p=document.getElementById(cid);
	timeDialog(function (dat,pNode){
		listAlarm.appendAlarm(dat,pNode.info_title,function (x){
			x.play();
		},pNode.children[1]);
	},p);
}

