function refreshRecent()
{
	var xhr=new XMLHttpRequest();
	xhr.open("POST","read.php");
	xhr.onreadystatechange =function(e)
	{
		if(this.readyState==4)
		{
			var R=document .getElementById("recent");
			R.innerHTML="";
			if(this.status==200)
			{
				//成功連線
				var A=JSON.parse(this.responseText);
				A.forEach(function (val,idx){
					createDomTree(R,[
						'li',null,'class','recentTitle','onclick','listYoutube.appendMedia("'+val['id']+'")',[
							'Text',null,val['title']+"("+val['count']+")"
						]
					]);
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
function reportRecent(id,st,title)
{
	var xhr=new XMLHttpRequest();
	xhr.open("POST","report.php");
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.onreadystatechange =function(e)
	{
		if(this.readyState==4 && this.status==200)	//成功連線
		{
			console.log(this.responseText);
			refreshRecent();
		}
	}
	xhr.send("id="+id+"&title="+title+"&st="+(st===true?"true":"false"));
}
