function refreshRecent()
{
	var xhr=new XMLHttpRequest();
	xhr.open("GET","api/YTRecordAPI.php");
	xhr.onreadystatechange =function(e)
	{
		if(this.readyState==4)
		{
			var recent=document .getElementById("recent");
			var hot=document .getElementById("hot");
			recent.innerHTML="";
			hot.innerHTML="";
			if(this.status==200)
			{
				var A=JSON.parse(this.responseText);
				A['new'].forEach(function (val,idx){
					var p=document.createElement("li");
					p.textContent=val['title'];
					p.setAttribute('data-vid',val['vid']);
					p.addEventListener('click',onClickRecent);
					recent.appendChild(p);
				});
				A['hot'].forEach(function (val,idx){
					var p=document.createElement("li");
					p.textContent=val['title']+' ('+val['hit']+')';
					p.setAttribute('data-vid',val['vid']);
					p.addEventListener('click',onClickRecent);
					hot.appendChild(p);
				});
			}
			else
			{
				//伺服器失效
				R.innerHTML="伺服器無法連線";
			}
		}
		
	}
	xhr.send();
}
function reportRecent(id,title)
{
	var xhr=new XMLHttpRequest();
	xhr.open("POST","api/YTRecordAPI.php");
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.onreadystatechange =function(e)
	{
		if(this.readyState==4)	//成功連線
		{
			console.log(this.status+":"+this.responseText);
			refreshRecent();
		}
	}
	xhr.send("vid="+id+"&title="+title);
}
function onClickRecent(evt)
{
	var tmp=document .getElementById("YTinput");
	var inp=tmp.getElementsByTagName('input')[0];
	inp.value=evt.target.dataset.vid;
	loadFromYT();
}