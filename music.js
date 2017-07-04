var listMusic;

function initMusicDiv()
{
	listMusic=new mediaList(
		"music",
		"icons/ic_alarm_add_black_24px.svg",
		"icons/ic_delete_forever_black_24px.svg",
		createMus,null,appMusAlarm);
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
	var p=document.getElementById(cid);
	timeDialog(function (dat,pNode){
		listAlarm.appendAlarm(dat,pNode.info_title,function (x){
			x.play();
		},pNode.children[1]);
	},p);
}

