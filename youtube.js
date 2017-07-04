var listYoutube;

function initYTDiv()
{
	listYoutube=new mediaList(
		"youtube",
		"icons/ic_alarm_add_black_24px.svg",
		"icons/ic_delete_forever_black_24px.svg",
		createYT,DelYT,appYTAlarm);
}
function createYT(cid,vid)
{
	var player=new YT.Player(cid,{
		height: '270',
		width: '480',
		videoId:vid,
		events:{
			'onReady':onPlayerReady,
			'onStateChange':onPlayerStateChange //重複撥放
		},
		playerVars: {rel: 0}
	});
	player.preID=vid;
}
function onPlayerReady(e)
{
	e.target.mute()
	e.target.playVideo();
}
function onPlayerStateChange(e)
{
	var vid=e.target.preID;
	console.log(JSON.stringify(e.target.getVideoData()));
	if(e.data==0)
	{
		e.target.seekTo(0);
	}
	else if(e.data==1 && e.target.isMuted())
	{
		e.target.pauseVideo();
		e.target.unMute();
		e.target.seekTo(0);
		reportRecent(vid,true,e.target.getVideoData().title);
	}
	else if(e.data==-1)
	{
		mediaList_removeMedia(e.target.getIframe().parentNode.id);
		reportRecent(vid,false,"");
	}
}
function DelYT(cid)
{
	var player=YT.get(cid);
	player.destroy();
}
function appYTAlarm(cid)
{
	var p=YT.get(cid);
	timeDialog(function (dat,player){
		listAlarm.appendAlarm(dat,
			player.getVideoData().title,
			function (x){x.playVideo();},
			player
		);
	},p);
}
