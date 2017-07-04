<?php

date_default_timezone_set('Asia/Taipei');
$vid=isset($_POST['id'])?$_POST['id']:null;
$st=isset($_POST['st'])?$_POST['st']:null;
$title=isset($_POST['title'])?$_POST['title']:"";



//檢查 vid
if($vid!==null)
{
	$code_a=ord('a');
	$code_z=ord('z');
	$code_A=ord('A');
	$code_Z=ord('Z');
	$code_0=ord('0');
	$code_9=ord('9');
	$code_1=ord('-');
	$code_2=ord('_');
	for($i=count($vid)-1;$i>=0;--$i)
	{
		$code=ord(substr($vid,$i,1));
		if( ($code_a > $code || $code > $code_z) &&
			($code_A > $code || $code > $code_Z) &&
			($code_0 > $code || $code > $code_9) &&
			($code_1 != $code && $code != $code_2) )
			break;
	}
	if($i>=0)
		$vid==false;
}
//檢查 st
if($st!=='true' && $st!=='false')
	$st=null;
echo json_encode([$vid,$st]);
if($st===null || $vid===null)
	exit(0);

$pdo=new PDO(
	"mysql:host=localhost;dbname=recentYT",
	"phptest",
	"811047"
);
if($st==='true')
{
	$mch=$pdo->query("SELECT * FROM ytlist WHERE id='$vid'");
	$mch->setFetchMode(PDO::FETCH_ASSOC);
	$tmp=$mch->fetchAll();
	if($mch->rowCount()==0)
		$pdo->query("INSERT INTO ytlist (id,time,count,title) VALUES ('$vid','".time()."','1','$title')");
	else
	{
		$pdo->query("UPDATE ytlist SET count=".((int)$tmp[0]['count']+1).
			",time=".time().",title='$title' WHERE id='$vid' ");
	}
}
else
{
	echo 'del:'.$vid.'<br>';
	$mch=$pdo->query("SELECT * FROM ytlist WHERE id='$vid'");
	if($mch->rowCount()!=0)
	{
		$pdo->query("DELETE FROM ytlist WHERE id='$vid'");
	}
}

$data=$pdo->query("SELECT * FROM ytlist ORDER BY time DESC");
$data->setFetchMode(PDO::FETCH_ASSOC);
$tmp=$data->fetchAll();
foreach($tmp as $row)
	print_r($row['id'].'    '.$row['time'].'    '.$row['count'].'<br>');

?>
