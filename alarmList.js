/*
物件 alarmList
建構
alarmList(containerId,imgDelSrc);
屬性
prefix(=containerId)
container
list(=[{
    time:Date,
    title:string,
    play:function
}]);
img
方法
appendAlarm(time,title,funcPlay);
removeAlarm(id);
execAll();
pauseAll();
*/
function alarmList(containerId,imgDelSrc,svgId)
{
	this.prefix=containerId;
	this.svgPrefix=svgId;
	this.list=[];
	this.count=0;
	this.img=imgDelSrc;
	var pNode=document .getElementById(containerId);
	pNode.refAlarmList=this;
	pNode=null;
	this.init_svg_clock();
	this.svg_sec=document .getElementById(this.svgPrefix+"_sec");
	this.svg_min=document .getElementById(this.svgPrefix+"_min");
	this.svg_hr=document .getElementById(this.svgPrefix+"_hr");
	this.itv=null;
	setTimeout(function (obj){
		this.itv=setInterval(function(obj2)
		{
			obj2.execAll();
		},1000,obj);
	},(1000-(new Date).getMilliseconds()),this);
	this.execAll();
	this.lock=false;
	
	console.log([this.svg_sec,this.svg_min,this.svg_hr]);
}
alarmList.prototype.appendAlarm=function(time,title,funcPlay,parameter)
{
	if(this.lock)
		return;
	time.getMilliseconds(999);
	var curId=this.prefix+"_"+this.count;
	createDomTree(document .getElementById(this.prefix),
		['div',null,'id',curId,'class','AlarmDiv',
			['div',null,'class','AlarmContent',
				['span',null,'class','alarm_time',['Text',null,[
					("00"+time.getHours()).slice(-2),("00"+time.getMinutes()).slice(-2)].join(':')]],
				['span',null,'class','alarm_title',['Text',null,title]]
			],
			['div',null,'class','AlarmCtrl',
				['img',null,'src',this.img,'onclick','alarmList_removeAlarm("'+curId+'")']
			]
		]
	);
	this.list.push({id:curId,time:time,title:title,play:funcPlay,param:parameter});
	++this.count;
}
alarmList.prototype.execAll=function ()
{
	var curTime=new Date();
	
	var deg=curTime.getSeconds()*6-90;
	this.svg_sec .setAttribute("d","M "+xypairs(-5,deg)+" L "+xypairs(35,deg));
	
	deg=(curTime.getMinutes()+(deg+90)/360)*6-90;
	this.svg_min .setAttribute("d","M 0,0 L "+xypairs(32,deg));
	
	deg=(curTime.getHours()+(deg+90)/360)*30-90;
	this.svg_hr .setAttribute("d","M 0,0 L "+xypairs(25,deg));
	
	
	var i,t;
	for(i=this.list.length-1;i>=0;--i)
	{
		t=this.list[i];
		if(t.time.getTime()<=curTime)
		{
			var ppp=t.play;
			var papa=t.param;
			alarmList_removeAlarm(t.id);
			if(ppp)
				ppp(papa);
			else
				console.log('play()==null');
		}
	}
}
function alarmList_removeAlarm(id)
{
	var target=document .getElementById(id);
	var obj=target.parentNode.refAlarmList;
	var i;
	for(i=obj.list.length-1;i>=0 && obj.list[i].id!=id;--i);
	if(i>=0)
		obj.list.splice(i,1);
	console.log(JSON.stringify(obj.list));
	target.parentNode.removeChild(target);
}
alarmList.prototype.init_svg_clock=function()
{
	var div=document .getElementById(this.svgPrefix);
	var ns="http://www.w3.org/2000/svg";
	var cir1="stroke:black;fill:white;stroke-width:1px";
	var point1="fill:black";
	var line1="stroke:black;fill:none;stroke-width:1px";
	var line2="stroke:black;fill:none;stroke-width:2px";
	var line3="stroke:red;fill:none;stroke-width:0.75px";
	createDomTree(div,
		['div',null,'style','text-align:center',
			['svg',ns,'id',this.svgPrefix+'_sclock','width','150','height','150','viewBox','-50 -50 100 100',
				['circle',ns,'r','45','style',cir1],
				['path',ns,'id',this.svgPrefix+'_min','d','M 0,0 '+xypairs(32,0),'style',line1],
				['path',ns,'id',this.svgPrefix+'_hr' ,'d','M 0,0 '+xypairs(25,45),'style',line2],
				['path',ns,'id',this.svgPrefix+'_sec','d','M '+xypairs(-5,90)+' L '+xypairs(35,90),'style',line3],
				['circle',ns,'r','2','style',point1]
			]
		]
	);
	var svg=document .getElementById(this.svgPrefix+'_sclock');
	var i;
	for(i=0;i<12;++i)
	{
		var theta=i*30;
		createDomTree(svg,
			['path',ns,'d','M '+xypairs(37,theta)+' L '+xypairs(i%3==0?45:42,theta),'style',
			i%3==0?line2:line1]
		);
	}
	
}
function xypairs(r,deg)
{
	return "".concat(r*Math.cos(deg/180*Math.PI),',',r*Math.sin(deg/180*Math.PI));
}
