<?php
require_once(__dir__ .'/../config.php');
require_once(__dir__ .'/dbClassLoader.php');

try {
    $pdo=new PDO(
        'mysql:host='.$cfg_db['host'].';dbname='.$cfg_db['dbname'],
        $cfg_db['user'],
        $cfg_db['password']
    );    
} catch(PDOException $e) {
    exit('error:'.$e);
}

$stat=$pdo->query('ALTER TABLE YTRecord ADD `hit` INT UNSIGNED NOT NULL');
if($stat===false){
    var_dump($pdo->errorInfo());
}

$stat=$pdo->query('UPDATE YTRecord SET `hit`=1');
if($stat===false){
    var_dump($pdo->errorInfo());
}

$stat=$pdo->query('ALTER TABLE YTRecord ADD ts BIGINT UNSIGNED NOT NULL');
if($stat===false){
    var_dump($pdo->errorInfo());
}

$stat=$pdo->query('UPDATE YTRecord SET `ts`='.time());
if($stat===false){
    var_dump($pdo->errorInfo());
}

$stat=$pdo->query('SELECT * FROM YTRecord');
if($stat===false){
    var_dump($pdo->errorInfo());
}
$r=$stat->fetchAll(PDO::FETCH_ASSOC);
echo '<pre>'.print_r($r,true).'</pre>';