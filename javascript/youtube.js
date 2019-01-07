var listYoutube;

function initYTDiv()
{
	listYoutube=new mediaList(
		"youtube",
		"imgs/ic_alarm_add_black_24px.svg",
		"imgs/ic_delete_forever_black_24px.svg",
		createYT,DelYT,appYTAlarm
	);
}
function enableYTAddAlarm()
{
	listYoutube.fAddAlarm=appYTAlarm;
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
	var title=e.target.getVideoData().title;
	var videoId=e.target.preID;
	reportRecent(videoId,title);
}
function onPlayerStateChange(e)
{
	if (e.data == YT.PlayerState.ENDED) {
		e.target.seekTo(0,false);
		e.target.playVideo();
	}
}
function DelYT(cid)
{
	var player=YT.get(cid);
	player.destroy();
}
function appYTAlarm(cid)
{
	if(!AutoEnable)
	{
		alert("無法設定鬧鐘，您的瀏覽器可能禁用了自動撥放！");
		return;
	}
	var p=YT.get(cid);
	timeDialog(function (dat,player){
		listAlarm.appendAlarm(dat,
			player.getVideoData().title,
			function (x){x.playVideo();},
			player
		);
	},p);
}
