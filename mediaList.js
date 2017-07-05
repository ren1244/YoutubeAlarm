/*
物件 mediaList
建構
mediaList(
    containerId,
    imgAddSrc,
    imgDelSrc,
    funcCreateMedia,
    funcAddAlarm
);
屬性
prefix(=containerId)
container
list(={
    Id:string	
})
count
imgAdd
imgDel
funcCreateMedia(參數:容器ref)
funcAddAlarm
方法
appendYT(time,title,funcPlay);
removeYT(id);
*/

function mediaList(containerId,imgAddSrc,imgDelSrc,funcCreateMedia,funcDestoryMedia,funcAddAlarm)
{
	this.prefix=containerId;
	this.list=[];
	document .getElementById(containerId).refMediaList=this;
	this.count=0;
	this.imgAdd=imgAddSrc;
	this.imgDel=imgDelSrc;
	this.fCreateMedia=funcCreateMedia;	//fCreateMedia(containerID,myVar)
	this.fDestoryMedia=funcDestoryMedia; //fCreateMedia(containerID)
	this.fAddAlarm=funcAddAlarm;
}
mediaList.prototype.appendMedia=function(myVar)
{
	var curId=this.prefix+"_"+this.count;
	createDomTree(document .getElementById(this.prefix),
		['div',null,'id',curId,'class','MediaDiv',
			['div',null,'id',curId+"_media",'class','MediaContent'],
			['div',null,'class','MediaControl',
				['img',null,'src',this.imgAdd,'onclick','mediaList_AddAlarm("'+curId+'")'],
				['img',null,'src',this.imgDel,'onclick','mediaList_removeMedia("'+curId+'")']
			]
		]
	);
	this.list.push(curId);
	++this.count;
	if(this.fCreateMedia)
		this.fCreateMedia(curId+"_media",myVar);
}
function mediaList_removeMedia(id)
{
	var target=document .getElementById(id);
	var obj=target.parentNode.refMediaList;
	var i;
	for(i=obj.list.length-1;i>=0 && obj.list[i]!=id;--i);
	if(i>=0)
		obj.list.splice(i,1);
	console.log(obj.list);
	if(obj.fDestoryMedia)
		obj.fDestoryMedia(id+"_media");
	target.parentNode.removeChild(target);
}
function mediaList_AddAlarm(id)
{
	var target=document .getElementById(id);
	var obj=target.parentNode.refMediaList;
	if(obj.fAddAlarm)
		obj.fAddAlarm(id+"_media");
}

