var xhr=new XMLHttpRequest();
//xhr.open('GET','http://localhost/YT/api/YTRecord.php');
xhr.open('POST','http://localhost/YT/api/YTRecord.php');
xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
xhr.onreadystatechange=function (){
 if(xhr.readyState==4){
	 console.log(xhr.status+":"+xhr.responseText);
	 if(xhr.status==200){
		 
	 }
 }
}
//xhr.send();
//xhr.send('url=https://www.youtube.com/abc');
xhr.send('vid=SL5RdSUMvcY&title=秒看完一生');
 