function createDomTree(p,DataArr)
{
/*根據陣列資料，建立整個DOM
	參數：
		p:掛載目的地，DOM Ref。
		DataArr:陣列，內容規定如下。
			
			[tagNmae,namespace,Attr1,Val1,Attr2,Val2,...,[ChildrenElement]]
			
			tagNmae:標籤名稱 如div p 等
			namespace:如果是一般html元素，填null，否則為xml namespace
			Attr:屬性名稱
			Val:屬性數值
			ChildrenElement:陣列，此tagDOM的代表子物件
*/
	var t,ty,i;
	if(typeof(DataArr[0])!='string')
	{
		for(i=0;i<DataArr.length;++i)
			if(typeof(DataArr[i])!='string')
				createDomTree(p,DataArr[i]);
		return;
	}
	if(DataArr[1])
	{
		p.appendChild(t=document.createElementNS(DataArr[1],DataArr[0]));
		for(i=2;i<DataArr.length;++i)
			if(typeof(DataArr[i])=='string')
			{
				t.setAttributeNS(null,DataArr[i],DataArr[i+1]);
				++i;
			}
			else
				createDomTree(t,DataArr[i]);
	}
	else
	{
		if(DataArr[0]=='Text')
			p.appendChild(t=document.createTextNode(DataArr.slice(2).toString()));
		else
		{
			p.appendChild(t=document.createElement(DataArr[0]));
			for(i=2;i<DataArr.length;++i)
				if(typeof(DataArr[i])=='string')
				{
					if(DataArr[i]=='innerHTML')
						t.innerHTML=DataArr[i+1];
					else
						t.setAttribute(DataArr[i],DataArr[i+1]);
					++i;
				}
				else
					createDomTree(t,DataArr[i]);
		}
	}
}

function getUrlFromFile(theFile,cbk)
{//傳入 file 物件，讀取後會把產生的 blob 物件傳給 cbk(url:blob)
	var freader=new FileReader();
	freader.onload=function(e){
		var blob=new Blob([e.target.result],{type:"audio/*"});
		var audio=window.URL.createObjectURL(blob);
		cbk(audio);
	};
	freader.readAsArrayBuffer(theFile);
}

function fileDialog(acceptType,cbk)
{ //產生選取檔案的對話盒，選取完後會執行 cbk(files:files)
	var f=document .createElement("input");
	f .setAttribute("type","file");
	if(acceptType)
		f.accept=acceptType;
	var evt=document.createEvent("MouseEvents");
	evt.initEvent("click", true, false);
	f.onchange=function (){
		cbk(f.files);
	}
	f.dispatchEvent(evt);
}

function timeDialog(cbk,parameter)
{ //產生輸入時間的對話盒，輸入正確的話會執行 cbk(date:date,parameter:object)
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
	
	cbk(d,parameter);
	/*
	if(cid.slice(0,3)=="you")
	{
		var tit=YT.get(cid).getVideoData().title;
		listAlarm.appendAlarm(d,tit,YTplayById(cid));
	}
	if(cid.slice(0,3)=="mus")
	{
		var tit=document .getElementById(cid).children[0].innerHTML;
		listAlarm.appendAlarm(d,tit,MusplayById(cid));
	}*/
}
