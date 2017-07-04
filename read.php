<?php

$pdo=new PDO(
	"mysql:host=localhost;dbname=recentYT",
	"phptest",
	"811047"
);
$q=$pdo->query("SELECT * FROM ytlist ORDER BY time DESC LIMIT 10");
$q->setFetchMode(PDO::FETCH_ASSOC);
$data=$q->fetchAll();
echo json_encode($data);
?>
